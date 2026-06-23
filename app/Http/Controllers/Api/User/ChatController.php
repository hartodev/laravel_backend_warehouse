<?php

// namespace App\Http\Controllers\Api\User;

// use App\Http\Controllers\Controller;
// use App\Models\Chat;
// use App\Models\ChatMessage;
// use Illuminate\Http\JsonResponse;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Validator;

// class ChatController extends Controller
// {
//    // GET /api/chats — daftar semua chat user ini
//     public function index(): JsonResponse
//     {
//         $userId = auth()->id();

//         $chats = Chat::with([
//                 'sender:id,name',
//                 'receiver:id,name',
//                 'latestMessage',
//             ])
//             ->where('sender_id', $userId)
//             ->orWhere('receiver_id', $userId)
//             ->orderByDesc('last_message_at')
//             ->get()
//             ->map(function ($chat) use ($userId) {
//                 // Tentukan lawan bicara
//                 $partner = $chat->sender_id === $userId ? $chat->receiver : $chat->sender;

//             return [
//                     'id'             => $chat->id,
//                     'partner'        => $partner,
//                     'latest_message' => $chat->latestMessage,
//                     'unread_count'   => $chat->unreadCount($userId),
//                     'last_message_at'=> $chat->last_message_at,
//                 ];
//             });

//         return response()->json(['success' => true, 'data' => $chats]);
//     }

//     // GET /api/chats/{chat} — detail chat + semua pesan
//     public function show(Chat $chat): JsonResponse
//     {
//         $userId = auth()->id();

//         // Pastikan user ini ada di chat ini
//         if ($chat->sender_id !== $userId && $chat->receiver_id !== $userId) {
//             return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
//         }

//         $messages = $chat->messages()
//             ->with('sender:id,name')
//             ->paginate(50);

//         return response()->json([
//             'success' => true,
//             'data'    => [
//                 'chat'     => $chat->load('sender:id,name', 'receiver:id,name'),
//                 'messages' => $messages,
//             ],
//         ]);
//     }

//     // POST /api/chats — buat chat baru atau ambil yang sudah ada
//     public function store(Request $request): JsonResponse
//     {
//         $validator = Validator::make($request->all(), [
//             'receiver_id' => 'required|exists:users,id|different:' . auth()->id(),
//             'message'     => 'required|string',
//         ]);

//         if ($validator->fails()) {
//             return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
//         }

//         $userId     = auth()->id();
//         $receiverId = $request->receiver_id;

//         // Cari chat yang sudah ada (dua arah)
//         $chat = Chat::where(function ($q) use ($userId, $receiverId) {
//             $q->where('sender_id', $userId)->where('receiver_id', $receiverId);
//         })->orWhere(function ($q) use ($userId, $receiverId) {
//             $q->where('sender_id', $receiverId)->where('receiver_id', $userId);
//         })->first();

//         if (! $chat) {
//             $chat = Chat::create([
//                 'sender_id'   => $userId,
//                 'receiver_id' => $receiverId,
//             ]);
//         }

//         // Kirim pesan pertama
//         $message = ChatMessage::create([
//             'chat_id'   => $chat->id,
//             'sender_id' => $userId,
//             'message'   => $request->message,
//         ]);

//         $chat->update(['last_message_at' => now()]);

//         return response()->json([
//             'success' => true,
//             'message' => 'Chat berhasil dibuat.',
//             'data'    => [
//                 'chat'    => $chat->load('sender:id,name', 'receiver:id,name'),
//                 'message' => $message,
//             ],
//         ], 201);
//     }

//     // // POST /api/chats/{chat}/messages — kirim pesan
//     // public function sendMessage(Request $request, Chat $chat): JsonResponse
//     // {
//     //     $userId = auth()->id();

//     //     if ($chat->sender_id !== $userId && $chat->receiver_id !== $userId) {
//     //         return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
//     //     }

//     //     $validator = Validator::make($request->all(), [
//     //         'message' => 'required|string',
//     //     ]);

//     //     if ($validator->fails()) {
//     //         return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
//     //     }

//     //     $message = ChatMessage::create([
//     //         'chat_id'   => $chat->id,
//     //         'sender_id' => $userId,
//     //         'message'   => $request->message,
//     //     ]);

//     //     $chat->update(['last_message_at' => now()]);

//     //     return response()->json([
//     //         'success' => true,
//     //         'message' => 'Pesan berhasil dikirim.',
//     //         'data'    => $message->load('sender:id,name'),
//     //     ], 201);
//     // }




//     public function sendMessage(Request $request, Chat $chat)
//     {
//         $request->validate(['message' => 'required|string']);

//         // Pastikan user adalah member chat ini
//         if ($chat->user_one_id !== auth()->id() && $chat->user_two_id !== auth()->id()) {
//             return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
//         }

//         $message = ChatMessage::create([
//             'chat_id'   => $chat->id,
//             'sender_id' => auth()->id(),
//             'message'   => $request->message,
//             'is_read'   => false,
//         ]);

//         $message->load('sender');

//         // Update last_message_at
//         $chat->update(['last_message_at' => now()]);

//         // Broadcast ke channel
//         broadcast(new \App\Events\MessageSent($message));

//         return response()->json([
//             'success' => true,
//             'message' => 'Pesan terkirim',
//             'data'    => $message,
//         ]);
//     }
//     // PUT /api/chats/{chat}/read — tandai semua pesan di chat ini sudah dibaca
//     public function markAsRead(Chat $chat): JsonResponse
//     {
//         $userId = auth()->id();

//         if ($chat->sender_id !== $userId && $chat->receiver_id !== $userId) {
//             return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
//         }

//         ChatMessage::where('chat_id', $chat->id)
//                    ->where('sender_id', '!=', $userId)
//                    ->where('is_read', false)
//                    ->update(['is_read' => true, 'read_at' => now()]);

//         return response()->json(['success' => true, 'message' => 'Semua pesan ditandai sudah dibaca.']);
//     }
// }



namespace App\Http\Controllers\Api\User;

use App\Events\MessageSent;
use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChatController extends Controller
{
    // GET /api/users — daftar semua user untuk dipilih
    public function users(Request $request)
    {
        $users = User::where('id', '!=', auth()->id())
            ->when(
                $request->search,
                fn($q, $s) =>
                $q->where('name', 'like', "%$s%")
                    ->orWhere('email', 'like', "%$s%")
            )
            ->select('id', 'name', 'email', 'role')
            ->orderBy('role')
            ->orderBy('name')
            ->get();

        return response()->json(['success' => true, 'data' => $users]);
    }

    // GET /api/chats — daftar chat milik user yg login
    public function index()
    {
        $userId = auth()->id();
        $chats  = Chat::where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->with([
                'latestMessage.sender:id,name,role',
                'userOne:id,name,email,role',
                'userTwo:id,name,email,role',
        ])
            ->orderByDesc('last_message_at')
            ->get()
            ->map(fn($chat) => $this->formatChat($chat, $userId));

        return response()->json(['success' => true, 'data' => $chats]);
    }

    // GET /api/chats/{chat}
    public function show(Chat $chat)
    {
        $userId = auth()->id();
        if (!in_array($userId, [$chat->user_one_id, $chat->user_two_id])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $messages = ChatMessage::where('chat_id', $chat->id)
            ->with('sender:id,name,role')
            ->orderBy('created_at')
            ->paginate(50);

        $chat->load(['userOne:id,name,email,role', 'userTwo:id,name,email,role']);

        return response()->json([
            'success' => true,
            'data' => [
                'chat'     => $this->formatChat($chat, $userId),
                'messages' => $messages,
            ],
        ]);
    }

    // POST /api/chats — buat chat baru + pesan pertama
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message'     => 'required|string|max:1000',
        ]);

        $userId     = auth()->id();
        $receiverId = $request->receiver_id;

        // user_one_id selalu ID lebih kecil (hindari duplikat)
        $oneId = min($userId, $receiverId);
        $twoId = max($userId, $receiverId);

        $chat = Chat::firstOrCreate(
            ['user_one_id' => $oneId, 'user_two_id' => $twoId],
            ['last_message_at' => now()]
        );

        $message = ChatMessage::create([
            'chat_id'   => $chat->id,
            'sender_id' => $userId,
            'message'   => $request->message,
            'is_read'   => false,
        ]);
        $message->load('sender:id,name,role');

        $chat->update(['last_message_at' => now()]);
        broadcast(new MessageSent($message));

        return response()->json([
            'success' => true,
            'message' => 'Chat dibuat',
            'data'    => [
                'chat'    => ['id' => $chat->id],
                'message' => $message,
            ],
        ], 201);
    }

    // POST /api/chats/{chat}/messages
    public function sendMessage(Request $request, Chat $chat)
    {
        $userId = auth()->id();
        if (!in_array($userId, [$chat->user_one_id, $chat->user_two_id])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate(['message' => 'required|string|max:1000']);

        $message = ChatMessage::create([
            'chat_id'   => $chat->id,
            'sender_id' => $userId,
            'message'   => $request->message,
            'is_read'   => false,
        ]);
        $message->load('sender:id,name,role');
        $chat->update(['last_message_at' => now()]);

        broadcast(new MessageSent($message));

        return response()->json([
            'success' => true,
            'message' => 'Pesan terkirim',
            'data'    => $message,
        ]);
    }

    // PUT /api/chats/{chat}/read
    public function markAsRead(Chat $chat)
    {
        $userId = auth()->id();
        ChatMessage::where('chat_id', $chat->id)
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true, 'message' => 'Sudah dibaca']);
    }

    private function formatChat(Chat $chat, int $myId): array
    {
        $partner = $chat->user_one_id === $myId ? $chat->userTwo : $chat->userOne;
        $unread  = ChatMessage::where('chat_id', $chat->id)
            ->where('sender_id', '!=', $myId)
            ->where('is_read', false)
            ->count();

        return [
            'id'              => $chat->id,
            'user_one_id'     => $chat->user_one_id,
            'user_two_id'     => $chat->user_two_id,
            'last_message_at' => $chat->last_message_at,
            'unread_count'    => $unread,
            'partner'         => $partner ? [
                'id'    => $partner->id,
                'name'  => $partner->name,
                'email' => $partner->email,
                'role'  => $partner->role,
            ] : null,
            'latest_message'  => $chat->latestMessage ? [
                'id'         => $chat->latestMessage->id,
                'chat_id'    => $chat->latestMessage->chat_id,
                'sender_id'  => $chat->latestMessage->sender_id,
                'message'    => $chat->latestMessage->message,
                'is_read'    => $chat->latestMessage->is_read,
                'created_at' => $chat->latestMessage->created_at,
            ] : null,
        ];
    }
}
