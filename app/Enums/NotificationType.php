<?php

namespace App\Enums;

enum NotificationType: string
{
    case TaskAssigned = 'task_assigned';
    case TaskCommented = 'task_commented';
    case TaskStatusChanged = 'task_status_changed';
    case ContentApproved = 'content_approved';
    case RevisionRequested = 'revision_requested';
    case ContentReadyForApproval = 'content_ready_for_approval';
    case ReportShared = 'report_shared';

    public function label(): string
    {
        return match ($this) {
            self::TaskAssigned => 'Task assigned to me',
            self::TaskCommented => 'Comments on my tasks',
            self::TaskStatusChanged => 'Task status changes',
            self::ContentApproved => 'Client approved content',
            self::RevisionRequested => 'Client requested a revision',
            self::ContentReadyForApproval => 'Content ready for my review',
            self::ReportShared => 'New performance report',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::TaskAssigned => 'When someone assigns a task to you.',
            self::TaskCommented => 'When someone comments on a task you\'re assigned to or manage.',
            self::TaskStatusChanged => 'When a task\'s status changes.',
            self::ContentApproved => 'When a client approves content you shared with them.',
            self::RevisionRequested => 'When a client requests changes to shared content.',
            self::ContentReadyForApproval => 'When your team shares new content for you to review.',
            self::ReportShared => 'When your team shares a new performance report with you.',
        };
    }

    /**
     * Notification types relevant to staff accounts (shown on the internal Account Settings page).
     *
     * @return NotificationType[]
     */
    public static function staffTypes(): array
    {
        return [
            self::TaskAssigned,
            self::TaskCommented,
            self::TaskStatusChanged,
            self::ContentApproved,
            self::RevisionRequested,
        ];
    }

    /**
     * Notification types relevant to client portal accounts.
     *
     * @return NotificationType[]
     */
    public static function clientTypes(): array
    {
        return [
            self::ContentReadyForApproval,
            self::ReportShared,
        ];
    }
}
