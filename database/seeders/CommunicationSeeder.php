<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommunicationSeeder extends Seeder
{
    /**
     * Seed chats, chat_messages, notifications, activity_logs.
     * Butuh users.
     */
    public function run(): void
    {
        $this->seedChats();
        $this->seedNotifications();
        $this->seedActivityLogs();
    }

    protected function seedChats(): void
    {
        $userIds = DB::table('users')->pluck('id')->all();
        $pairs = [];

        // Buat 10 pasangan chat unik (user_one_id selalu lebih kecil dari user_two_id, sesuai konvensi)
        $attempts = 0;
        while (count($pairs) < 10 && $attempts < 100) {
            $attempts++;
            $two = fake()->randomElements($userIds, 2);
            sort($two);
            [$userOne, $userTwo] = $two;

            if ($userOne === $userTwo) {
                continue;
            }

            $key = $userOne . '-' . $userTwo;
            if (isset($pairs[$key])) {
                continue;
            }

            $pairs[$key] = [$userOne, $userTwo];
        }

        foreach ($pairs as [$userOne, $userTwo]) {
            $chatId = DB::table('chats')->insertGetId([
                'user_one_id' => $userOne,
                'user_two_id' => $userTwo,
                'last_message_at' => now()->subHours(fake()->numberBetween(1, 200)),
                'created_at' => now()->subDays(fake()->numberBetween(1, 30)),
                'updated_at' => now(),
            ]);

            $this->seedChatMessages($chatId, $userOne, $userTwo);
        }
    }

    protected function seedChatMessages(int $chatId, int $userOne, int $userTwo): void
    {
        $rows = [];
        $messageCount = fake()->numberBetween(2, 10);

        for ($i = 0; $i < $messageCount; $i++) {
            $sender = fake()->randomElement([$userOne, $userTwo]);
            $isRead = fake()->boolean(70);

            $rows[] = [
                'chat_id' => $chatId,
                'sender_id' => $sender,
                'message' => fake('id_ID')->sentence(fake()->numberBetween(3, 12)),
                'is_read' => $isRead,
                'read_at' => $isRead ? now()->subHours(fake()->numberBetween(1, 100)) : null,
                'created_at' => now()->subHours(fake()->numberBetween(1, 200)),
                'updated_at' => now(),
            ];
        }

        DB::table('chat_messages')->insert($rows);
    }

    protected function seedNotifications(): void
    {
        $userIds = DB::table('users')->pluck('id')->all();
        $types = [
            'App\\Notifications\\PurchaseOrderApproved',
            'App\\Notifications\\RequestStatusChanged',
            'App\\Notifications\\StockTransferReceived',
            'App\\Notifications\\BudgetRequestVerified',
        ];

        $rows = [];
        foreach (range(1, 40) as $i) {
            $isRead = fake()->boolean(60);

            $rows[] = [
                'user_id' => fake()->randomElement($userIds),
                'type' => fake()->randomElement($types),
                'title' => fake('id_ID')->sentence(4),
                'body' => fake('id_ID')->sentence(10),
                'data' => json_encode(['id' => fake()->numberBetween(1, 100)]),
                'is_read' => $isRead,
                'read_at' => $isRead ? now()->subDays(fake()->numberBetween(0, 10)) : null,
                'created_at' => now()->subDays(fake()->numberBetween(0, 20)),
                'updated_at' => now(),
            ];
        }

        DB::table('notifications')->insert($rows);
    }

    protected function seedActivityLogs(): void
    {
        $userIds = DB::table('users')->pluck('id')->all();
        $activities = ['create', 'update', 'delete', 'login', 'approve', 'reject'];
        $modules = ['Product', 'PurchaseOrder', 'Request', 'StockTransfer', 'SalesOrder', 'BudgetRequest', 'User'];

        $rows = [];
        foreach (range(1, 50) as $i) {
            $rows[] = [
                'user_id' => fake()->randomElement($userIds),
                'activity' => fake()->randomElement($activities),
                'module' => fake()->randomElement($modules),
                'subject_id' => fake()->numberBetween(1, 50),
                'description' => fake('id_ID')->sentence(),
                'old_values' => json_encode(['status' => 'pending']),
                'new_values' => json_encode(['status' => 'approved']),
                'ip_address' => fake()->ipv4(),
                'user_agent' => fake()->userAgent(),
                'created_at' => now()->subDays(fake()->numberBetween(0, 30)),
            ];
        }

        DB::table('activity_logs')->insert($rows);
    }
}
