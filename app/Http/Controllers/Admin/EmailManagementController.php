<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Webklex\IMAP\Facades\Client;
class EmailManagementController extends Controller
{
    /**
     * Display the list of emails.
     *
     * @return \Illuminate\Http\Response
     */
    public function showEmail()
    {
        // Initialize the client
        $client = Client::account('default');

        // Connect to the server
        $client->connect();

        // Get the default mailbox (INBOX)
        $mailbox = $client->getFolder('INBOX');

        // Initialize an array to hold the emails
        $emails = [];

        // Get all messages from the mailbox
        $messages = $mailbox->messages()->all()->get();

        foreach ($messages as $message) {
            $emails[] = [
                'subject' => $message->getSubject(),
                // 'from'    => $message->getFrom()[0]->mail,
                'body'    => $message->getTextBody(),
                'date'    => $message->getDate(),
            ];
        }

        // Return the emails as a JSON response
        return response()->json($emails);
    }

}
