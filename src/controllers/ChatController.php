<?php

namespace App\controllers;

use App\models\ChatChannel;
use App\models\Message;
use App\models\User;
use App\repositories\ChatChannelRepository;
use App\repositories\MessageRepository;
use App\repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ChatController extends Controller
{
    public function index(ChatChannelRepository $channelRepository)
    {
        /* @var $loggedUser User */
        $loggedUser = $_SESSION['user'];
        $loggedUserId = $loggedUser->getId();

        $response = $channelRepository->fetchManyWithLastMessage($loggedUserId);

        return $this->parseView('logged/list.html.twig', compact('response'));
    }

    public function create(Request $request, ChatChannelRepository $channelRepository, UserRepository $userRepository, MessageRepository $messageRepository)
    {
        /**
         * @var $sessionUser User
         * @var $existingChat ChatChannel
         */
        $sessionUser = $_SESSION['user'];
        $loggedId = $sessionUser->getId();
        $input = $request->request->all();
        $failed = empty($input['receiver'])
            || is_null($userRepository->fetchOne(['id' => $input['receiver']]));

        if ($failed) {
            return new Response("Bad request", 400);
        }

        $existingChat = $channelRepository->fetchOne([
            'user_from|user_to' => $loggedId,
            'user_to|user_from' => $input['receiver']
        ]);

        if (! is_null($existingChat)) {
            if (empty($input['message'])) {
                return new Response("Bad request", 400);
            }
            return $this->send($request, $existingChat->getId(), $channelRepository, $messageRepository);
        }

        try {
            $channel = (new ChatChannel())
                ->setUserFrom($loggedId)
                ->setUserTo($input['receiver']);
            $channelRepository->create($channel);

            if (! empty($input['message'])) {

                $message = (new Message())
                    ->setMessage($input['message'])
                    ->setChannelId($channel->getId())
                    ->setCreatorId($loggedId)
                    ->setSeen(false);
                $messageRepository->create($message);
            }
            $response = $channel->getId();
            $code = 201;
        } catch (\Exception $e) {
            $response = "Partial success";
            $code = 206;
        } finally {
            return new Response($response, $code);
        }
    }

    public function detail(int $id, ChatChannelRepository $channelRepository)
    {
        /* @var $loggedUser User */
        $loggedUser = $_SESSION['user'];
        $chat = $channelRepository->fetchChat($id, $loggedUser->getId());
        return json_encode($chat);
    }

    public function send(Request $request, int $id, ChatChannelRepository $channelRepository, MessageRepository $messageRepository)
    {
        /**
         * @var $sessionUser User
         */
        $sessionUser = $_SESSION['user'];
        $loggedId = $sessionUser->getId();
        $messageInput = $request->request->get('message');
        $failed =
            empty($messageInput)
            || is_null($channelRepository->fetchOne(['id' => $id]));

        if ($failed) {
            return new Response("Bad request", 400);
        }
        $response = "";
        $code = 0;
        try {
            $message = (new Message())
                ->setMessage($messageInput)
                ->setChannelId($id)
                ->setCreatorId($loggedId)
                ->setSeen(0);

            $messageRepository->create($message);
            $response = "Message sent";
            $code = 201;
        } catch (\Exception $e) {
            $response = "Internal Server Error";
            $code = 500;
        } finally {
            return new Response($response, $code);
        }
    }

    public function fetchNew(Request $request, int $id)
    {

    }

    public function getNewChatUsers(ChatChannelRepository $repository)
    {
        /* @var $loggedUser User */
        $loggedUser = $_SESSION['user'];

        $response = $repository->fetchAvailableUsers($loggedUser->getId());
        return json_encode($response);
    }
}
