<?php

namespace App\Support;

use App\Helpers\RoleHelper;
use App\Models\ConvertedLead;
use App\Models\SupportFlag;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SupportFlagFieldSupport
{
    public static function canUserUpdateSupportFlag(): bool
    {
        return RoleHelper::is_admin_or_super_admin()
            || RoleHelper::is_admission_counsellor()
            || RoleHelper::is_academic_assistant()
            || RoleHelper::is_support_team();
    }

    public static function supportFlagUpdateDeniedResponse(): array
    {
        return [
            'success' => false,
            'error' => 'You do not have permission to update the support flag.',
        ];
    }

    public static function supportFlagUpdateJsonResponse(ConvertedLead $convertedLead, $value): JsonResponse
    {
        $result = self::updateOnConvertedLead($convertedLead, $value);

        return response()->json($result, ! empty($result['success']) ? 200 : 403);
    }

    public static function forFilterSelect(): Collection
    {
        return SupportFlag::orderBy('title')->get(['id', 'title']);
    }

    public static function applyListingFilter(Builder $query, Request $request): void
    {
        if ($request->filled('support_flag_id')) {
            $query->where('support_flag_id', $request->support_flag_id);
        }
    }

    public static function displayHtml(?SupportFlag $supportFlag): string
    {
        if (! $supportFlag) {
            return '<span class="text-muted">N/A</span>';
        }

        $title = e($supportFlag->title);
        $color = e($supportFlag->color);

        return '<span class="d-inline-flex align-items-center gap-2 support-flag-display">'
            . '<span class="rounded border flex-shrink-0" style="width:18px;height:18px;background-color:' . $color . ';"></span>'
            . '<span class="fw-medium">' . $title . '</span>'
            . '</span>';
    }

    public static function updateOnConvertedLead(ConvertedLead $convertedLead, $value): array
    {
        if (! self::canUserUpdateSupportFlag()) {
            return self::supportFlagUpdateDeniedResponse();
        }

        $convertedLead->support_flag_id = $value ?: null;
        $convertedLead->save();

        $supportFlag = $value ? SupportFlag::find($value) : null;

        return [
            'success' => true,
            'message' => 'Updated successfully',
            'value' => $supportFlag ? $supportFlag->title : 'N/A',
            'display_html' => self::displayHtml($supportFlag),
            'support_flag_color' => $supportFlag?->color,
        ];
    }

    public static function validationRule(): string
    {
        return 'nullable|exists:support_flags,id';
    }
}
