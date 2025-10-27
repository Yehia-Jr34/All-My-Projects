<?php

namespace App\Http\Controllers;

use App\Events\DoctorSentMessage;
use App\Events\UserSentMessage;
use App\Models\Conversation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function create_chat(Request $request)
    {
        $user = request()->user();
        $user_id = $user->id;
        $doctor_id = $request->doctor_id;
        $filePath = 'storage/chats/' . $user_id . '-' . $doctor_id . '-Chat.json';

        Conversation::create([
            'user_id' => $user_id,
            'doctor_id' => $doctor_id,
            'path' => $filePath
        ]);

        json_encode([], JSON_PRETTY_PRINT);
        json_decode(Storage::put($filePath, '[]'), false);

        return response()->json([
            'message' => 'JSON file created successfully',
            'file_path' => $filePath,
        ]);
    }

    public function send(Request $request)
    {
        $user = request()->user();
        $message_text = $request->message;
        $carbon = Carbon::now()->addHours(3)->toDateTimeString();

        $conversation = Conversation::where('user_id', $request->user_id)->where('doctor_id', $request->doctor_id)->first();

        if ($user->name != null) {
            $message = [
                'name' => $user->name,
                'role' => 'user',
                'message' => $message_text,
                'time' => $carbon
            ];
            event(new UserSentMessage($user->id, $conversation->doctor_id, $message_text, $conversation->id));
        } else {
            $message = [
                'name' => $user->firstName . " " . $user->lastName,
                'role' => 'doctor',
                'message' => $message_text,
                'time' => $carbon
            ];
            event(new DoctorSentMessage($user->id, $conversation->user_id, $message_text, $conversation->id));
        }

        $path = $conversation->path;
        $jsonFile = Storage::get($path);
        $jsonData = json_decode($jsonFile, true);
        $jsonData[] = [$message];
        $jsonFile = json_encode($jsonData);
        Storage::put($path, $jsonFile);

        return response()->json([
            'message' => 'Message sent'
        ]);
    }

    function getChatMessages(Request $request)
    {
        $conversation = Conversation::where('user_id', $request->user_id)->where('doctor_id', $request->doctor_id)->first();

        $path = $conversation->path;

        $chatMessages = json_decode(Storage::get($path), true);
        return response()->json([
            'messages' => $chatMessages
        ]);
    }
}