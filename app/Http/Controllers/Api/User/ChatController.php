<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\ChatMessage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
   // GET /api/chats — daftar semua chat user ini
    public function index(): JsonResponse
    {
        $userId = auth()->id();
 
        $chats = Chat::with([
                'sender:id,name',
                'receiver:id,name',
                'latestMessage',
            ])
            ->where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderByDesc('last_message_at')
            ->get()
            ->map(function ($chat) use ($userId) {
                // Tentukan lawan bicara
                $partner = $chat->sender_id === $userId ? $chat->receiver : $chat->sender;
 
                return [
                    'id'             => $chat->id,
                    'partner'        => $partner,
                    'latest_message' => $chat->latestMessage,
                    'unread_count'   => $chat->unreadCount($userId),
                    'last_message_at'=> $chat->last_message_at,
                ];
            });
 
        return response()->json(['success' => true, 'data' => $chats]);
    }
 
    // GET /api/chats/{chat} — detail chat + semua pesan
    public function show(Chat $chat): JsonResponse
    {
        $userId = auth()->id();
 
        // Pastikan user ini ada di chat ini
        if ($chat->sender_id !== $userId && $chat->receiver_id !== $userId) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }
 
        $messages = $chat->messages()
            ->with('sender:id,name')
            ->paginate(50);
 
        return response()->json([
            'success' => true,
            'data'    => [
                'chat'     => $chat->load('sender:id,name', 'receiver:id,name'),
                'messages' => $messages,
            ],
        ]);
    }
 
    // POST /api/chats — buat chat baru atau ambil yang sudah ada
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|exists:users,id|different:' . auth()->id(),
            'message'     => 'required|string',
        ]);
 
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }
 
        $userId     = auth()->id();
        $receiverId = $request->receiver_id;
 
        // Cari chat yang sudah ada (dua arah)
        $chat = Chat::where(function ($q) use ($userId, $receiverId) {
            $q->where('sender_id', $userId)->where('receiver_id', $receiverId);
        })->orWhere(function ($q) use ($userId, $receiverId) {
            $q->where('sender_id', $receiverId)->where('receiver_id', $userId);
        })->first();
 
        if (! $chat) {
            $chat = Chat::create([
                'sender_id'   => $userId,
                'receiver_id' => $receiverId,
            ]);
        }
 
        // Kirim pesan pertama
        $message = ChatMessage::create([
            'chat_id'   => $chat->id,
            'sender_id' => $userId,
            'message'   => $request->message,
        ]);
 
        $chat->update(['last_message_at' => now()]);
 
        return response()->json([
            'success' => true,
            'message' => 'Chat berhasil dibuat.',
            'data'    => [
                'chat'    => $chat->load('sender:id,name', 'receiver:id,name'),
                'message' => $message,
            ],
        ], 201);
    }
 
    // POST /api/chats/{chat}/messages — kirim pesan
    public function sendMessage(Request $request, Chat $chat): JsonResponse
    {
        $userId = auth()->id();
 
        if ($chat->sender_id !== $userId && $chat->receiver_id !== $userId) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }
 
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
        ]);
 
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }
 
        $message = ChatMessage::create([
            'chat_id'   => $chat->id,
            'sender_id' => $userId,
            'message'   => $request->message,
        ]);
 
        $chat->update(['last_message_at' => now()]);
 
        return response()->json([
            'success' => true,
            'message' => 'Pesan berhasil dikirim.',
            'data'    => $message->load('sender:id,name'),
        ], 201);
    }
 
    // PUT /api/chats/{chat}/read — tandai semua pesan di chat ini sudah dibaca
    public function markAsRead(Chat $chat): JsonResponse
    {
        $userId = auth()->id();
 
        if ($chat->sender_id !== $userId && $chat->receiver_id !== $userId) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak.'], 403);
        }
 
        ChatMessage::where('chat_id', $chat->id)
                   ->where('sender_id', '!=', $userId)
                   ->where('is_read', false)
                   ->update(['is_read' => true, 'read_at' => now()]);
 
        return response()->json(['success' => true, 'message' => 'Semua pesan ditandai sudah dibaca.']);
    }
}
