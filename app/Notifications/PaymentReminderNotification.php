<?php

namespace App\Notifications;

use App\Models\PaymentSchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public PaymentSchedule $paymentSchedule,
        public string $reminderType = 'due' // 'due' or 'overdue'
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $enrollment = $this->paymentSchedule->enrollment;
        $student = $enrollment->student;
        
        $subject = $this->reminderType === 'overdue' 
            ? 'Overdue Payment Reminder - Shiloh Learning Center'
            : 'Payment Due Reminder - Shiloh Learning Center';

        $greeting = $this->reminderType === 'overdue'
            ? 'Payment Overdue Notice'
            : 'Payment Due Reminder';

        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line("This is a reminder regarding the payment for {$student->full_name}.")
            ->line("Package: {$enrollment->package->name}")
            ->line("Amount Due: ₱" . number_format($this->paymentSchedule->amount_due, 2))
            ->line("Due Date: " . $this->paymentSchedule->due_date->format('F d, Y'))
            ->line('Please settle this payment at your earliest convenience.')
            ->line('Note: Payments are non-refundable as per the package terms.')
            ->action('View Payment Details', url('/admin'))
            ->line('Thank you for your continued trust in Shiloh Learning and Development Center.');
    }
}
