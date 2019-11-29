<?php

namespace App\controllers;

use App\models\ChatChannel;
use App\repositories\ChatChannelRepository;
use App\repositories\UserRepository;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(ChatChannelRepository $channelRepository, UserRepository $userRepository)
    {
        $loggedUser = $_SESSION['user']->getId();
        $filters = [
            'user_from|user_to' => $loggedUser
        ];
        $channels = $channelRepository->fetchMany($filters);
        $users = $userRepository->fetchMany(array_map(function ($channel) use ($loggedUser) /* @var $channel ChatChannel */ {
            return $channel->getUserFrom() != $loggedUser ? $channel->getUserFrom() : $channel->getUserTo();
        }, $channels));
        return $this->parseView('logged/list.html.twig', compact('channels', 'users'));
    }

    public function detail(int $id)
    {

    }

    public function send(Request $request, int $id)
    {

    }

    public function fetchNew(Request $request, int $id)
    {

    }
}
