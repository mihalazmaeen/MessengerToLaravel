<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Facebook\GraphAPI;
use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\GraphNodes\GraphMessage;

class FacebookController extends Controller
{
    public function index(Request $request)
    {
        try {
            $fb = new Facebook([
                'app_id' => '702075561783308',
                'app_secret' => '6fdaae80ab511fbcdabb6dac27b7b53e',
                'default_graph_version' => 'v18.0',
            ]);
             
            $accessToken = 'EAAJZBiLR3MAwBOyooekZCLKzDhgkWFrMxWHJ6TVgUjOZB0DL1dfK7kt1ZCt0MfT9QFgKx0YlzJbMgqp1FcT95ZA6JUF6VihmO8irCWbGot7Re1kewns5T1p0SO1cTDMrrHbVz9qlPs38r2vZC8EP9oZAQbMHJaU6eZCZARSDdZA74jxghNZCJVkwbX0OCSaZBqTBcFCONFsZBt0tFPG9sD0lv2YBoA2hXY3ejcQ8ZD';

            $fb->setDefaultAccessToken($accessToken);
             

            // Fetch conversations
            $response = $fb->get('/me/conversations?fields=messages{message,from}', $accessToken);
           



            $conversations = $response->getGraphEdge();

            $messages = [];

            // Iterate through conversations and retrieve messages
            foreach ($conversations as $conversation) {
                $conversationId = $conversation['id'];

                $messagesResponse = $fb->get("/{$conversationId}/messages?fields=message,from", $accessToken);

                $conversationMessages = $messagesResponse->getGraphEdge();
                // dd($conversationMessages);

                // Append messages to the array
                foreach ($conversationMessages as $message) {
                    $messages[] = [
                        'sender' => $message['from']['name'],
                        'message' => $message['message'],
                    ];
                }
            }

        //    return response()->json(['messages' => $messages]);
        return view('welcome', compact('messages'));

        } catch (FacebookResponseException $e) {
            // Handle API response errors
            return response()->json(['error' => 'Facebook API Error: ' . $e->getMessage()], 500);
        } catch (FacebookSDKException $e) {
            // Handle SDK-related errors
            return response()->json(['error' => 'Facebook SDK Error: ' . $e->getMessage()], 500);
        }
    }
}