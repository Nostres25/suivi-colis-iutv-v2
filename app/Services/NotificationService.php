<?php

namespace App\Services;

use App\Mail\OrderNotificationMail;
use App\Models\Order;
use App\Models\User;
use App\Enums\NotificationReason;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public static function notify(NotificationReason $reason, Order $order, User $actor, ?string $extraMessage = null): void
    {
        $recipients = self::getRecipients($order, $actor);

        foreach ($recipients as $recipient) {
            $email = $recipient->getEmail();
            if (!$email) {
                continue;
            }

            try {
                Mail::to($email)->send(new OrderNotificationMail($reason, $order, $recipient, $actor, $extraMessage));
            } catch (\Throwable $e) {
                Log::error("Échec envoi mail [{$reason->value}] à {$email}: {$e->getMessage()}");
            }
        }
    }

    private static function getRecipients(Order $order, User $actor): \Illuminate\Support\Collection
    {
        $department = $order->getDepartment();
        $recipients = $department->getUsers();

        $author = $order->getAuthor();
        if ($author && !$recipients->contains('id', $author->getId())) {
            $recipients->push($author);
        }

        return $recipients->reject(fn (User $user) => $user->getId() == $actor->getId());
    }
}
