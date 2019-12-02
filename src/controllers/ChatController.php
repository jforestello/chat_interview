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
    public function index(ChatChannelRepository $channelRepository, UserRepository $userRepository, MessageRepository $messageRepository)
    {
        /* @var $loggedUser User */
        $loggedUser = $_SESSION['user'];
        $loggedUserId = $loggedUser->getId();
        $filters = [
            'user_from|user_to' => $loggedUserId
        ];
        $channels = $channelRepository->fetchMany($filters);
        $lastMessages = [];
        $usersFilter = [
            'id' => []
        ];
        array_map(function (ChatChannel $channel) use ($loggedUserId, &$lastMessages, &$usersFilter, $messageRepository) {
            $lastMessages[$channel->getId()] = $messageRepository->fetchOne(['channel_id' => $channel->getId()], ['id' => 'DESC']);
            $usersFilter['id'][] = $channel->getUserFrom() != $loggedUserId ? $channel->getUserFrom() : $channel->getUserTo();
        }, $channels);
        $users = $userRepository->fetchMany($usersFilter);
        $data = array_map(function (ChatChannel $channel) use ($loggedUserId, $lastMessages, $users) {
            /* @var $user User */
            $user = array_search($channel->getUserFrom() != $loggedUserId ? $channel->getUserFrom() : $channel->getUserTo(), array_column($users, 'id'));
            $message = $lastMessages[$channel->getId()];
            return [
                'chat_id' => $channel->getId(),
                'user' => $user,
                'last_message' => $message->getMessage(),
                'owner_user' => $message->getCreatorId() != $loggedUserId,
                'is_seen' => $message->isSeen(),
                'acronym' => $user->getFirstName()[0] . $user->getLastName()[0]
            ];
        }, $channels);

        return $this->parseView('logged/list.html.twig', compact('data'));
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
        $failed =
            empty($input['message'])
            || empty($input['receiver'])
            || is_null($userRepository->fetchOne(['id' => $input['receiver']]));

        if ($failed) {
            return new Response("Bad request", 400);
        }

        $existingChat = $channelRepository->fetchOne([
            'user_from|user_to' => $loggedId,
            'user_to|user_from' => $input['receiver']
        ]);

        if (! is_null($existingChat)) {
            return $this->send($request, $existingChat->getId(), $channelRepository, $messageRepository);
        }

        try {
            $channel = (new ChatChannel())
                ->setUserFrom($loggedId)
                ->setUserTo($input['receiver']);
            $channelRepository->create($channel);
            $message = (new Message())
                ->setMessage($input['message'])
                ->setChannelId($channel->getId())
                ->setCreatorId($loggedId)
                ->setSeen(false);

            $messageRepository->create($message);
            $response = "Message sent";
            $code = 201;
        } catch (\Exception $e) {
            $response = "Partial success";
            $code = 206;
        } finally {
            return new Response($response, $code);
        }
    }

    public function detail(int $id)
    {

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

        try {
            $message = (new Message())
                ->setMessage($messageInput)
                ->setChannelId($id)
                ->setCreatorId($loggedId)
                ->setSeen(false);

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
}
