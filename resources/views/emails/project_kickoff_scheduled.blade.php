@php
    $modeLabels = [
        'onsite' => 'Onsite',
        'virtual_meet' => 'Virtual - Meet',
        'virtual_teams' => 'Virtual - Teams',
    ];
    $meetingMode = $modeLabels[$kickoff->meeting_mode] ?? $kickoff->meeting_mode ?? '-';
    $scheduledAt = $kickoff->scheduled_at?->format('Y-m-d H:i') ?? '-';
    $meetingLinkLabel = [
        'virtual_meet' => 'Join Google Meet',
        'virtual_teams' => 'Join Microsoft Teams',
    ][$kickoff->meeting_mode] ?? 'Join Meeting';
    $products = $project->products?->pluck('name')->filter()->values() ?? collect();
    $stakeholders = $kickoff->stakeholderLinks?->pluck('stakeholder.name')->filter()->values() ?? collect();
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kick-off Scheduled</title>
</head>
<body style="margin:0;padding:0;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0;padding:0;width:100%;background:#eaf2ff;font-family:Arial,Helvetica,sans-serif;">
        <tr>
            <td align="center" style="padding:0;margin:0;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:900px;margin:0 auto;">
                    <tr>
                        <td style="height:10px;background:linear-gradient(90deg,#0ea5e9,#6366f1,#22c55e);line-height:10px;font-size:0;">
                            &nbsp;
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:0;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#ffffff;border:1px solid #d8e3f5;border-top:none;">
                                <tr>
                                    <td style="padding:18px 18px 12px 18px;background:#ffffff;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                            <tr>
                                                <td style="font-size:22px;font-weight:800;color:#0f172a;vertical-align:middle;">
                                                    FiscalOx
                                                </td>
                                                <td align="right" style="vertical-align:middle;">
                                                    <img src="https://secure.fiscalox.com/resources/img/logo.png" alt="FiscalOx Logo" height="84" style="display:block;border:0;outline:none;text-decoration:none;">
                                                </td>
                                            </tr>
                                        </table>

                                        <div style="height:1px;background:#e6eefc;margin-top:14px;"></div>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:18px 18px 6px 18px;">
                                        <div style="text-align:center;font-size:22px;font-weight:900;color:#0b1220;line-height:1.25;">
                                            Kick-off Scheduled: {{ $project->name }}
                                        </div>

                                        <div style="margin:14px auto 0 auto;width:92%;max-width:520px;background:#f1f7ff;border:1px solid #dbeafe;border-radius:14px;padding:12px 14px;">
                                            <div style="text-align:center;color:#1f2a44;font-size:14px;line-height:1.5;">
                                                <strong>Hello Team,</strong><br>
                                                Please find the kick-off details below.
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:18px 18px 0 18px;">
                                        <div style="font-size:16px;font-weight:800;color:#0f172a;margin-bottom:10px;">
                                            Kick-off Details
                                        </div>

                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse:separate;border-spacing:0 10px;">
                                            <tr>
                                                <td style="background:#fbfdff;border:1px solid #e6eefc;border-radius:14px;padding:14px 14px;">
                                                    <div style="font-size:12px;letter-spacing:0.4px;text-transform:uppercase;color:#64748b;font-weight:700;">
                                                        Date &amp; Time
                                                    </div>
                                                    <div style="margin-top:6px;font-size:16px;font-weight:800;color:#0f172a;">
                                                        {{ $scheduledAt }}
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td style="background:#fbfdff;border:1px solid #e6eefc;border-radius:14px;padding:14px 14px;">
                                                    <div style="font-size:12px;letter-spacing:0.4px;text-transform:uppercase;color:#64748b;font-weight:700;">
                                                        Mode
                                                    </div>
                                                    <div style="margin-top:6px;font-size:16px;font-weight:800;color:#0f172a;">
                                                        {{ $meetingMode }}
                                                    </div>
                                                </td>
                                            </tr>

                                            @if ($kickoff->meeting_mode === 'onsite')
                                                <tr>
                                                    <td style="background:#fbfdff;border:1px solid #e6eefc;border-radius:14px;padding:14px 14px;">
                                                        <div style="font-size:12px;letter-spacing:0.4px;text-transform:uppercase;color:#64748b;font-weight:700;">
                                                            Site Location
                                                        </div>
                                                        <div style="margin-top:6px;font-size:16px;font-weight:800;color:#0f172a;">
                                                            {{ $kickoff->site_location ?: '-' }}
                                                        </div>
                                                    </td>
                                                </tr>
                                            @else
                                                <tr>
                                                    <td style="background:#fbfdff;border:1px solid #e6eefc;border-radius:14px;padding:14px 14px;">
                                                        <div style="font-size:12px;letter-spacing:0.4px;text-transform:uppercase;color:#64748b;font-weight:700;">
                                                            Meeting Link
                                                        </div>
                                                        <div style="margin-top:8px;">
                                                            @if ($kickoff->meeting_link)
                                                                <a href="{{ $kickoff->meeting_link }}" style="display:inline-block;background:#1d4ed8;color:#ffffff;text-decoration:none;font-size:14px;font-weight:800;padding:10px 14px;border-radius:12px;">
                                                                    {{ $meetingLinkLabel }}
                                                                </a>
                                                            @else
                                                                <span style="display:inline-block;color:#475569;font-size:14px;font-weight:800;">
                                                                    Not provided
                                                                </span>
                                                            @endif
                                                        </div>
                                                        @if ($kickoff->meeting_link)
                                                            <div style="margin-top:10px;color:#475569;font-size:12px;line-height:1.4;word-break:break-all;">
                                                                {{ $kickoff->meeting_link }}
                                                            </div>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endif

                                            <tr>
                                                <td style="background:#fbfdff;border:1px solid #e6eefc;border-radius:14px;padding:14px 14px;">
                                                    <div style="font-size:12px;letter-spacing:0.4px;text-transform:uppercase;color:#64748b;font-weight:700;">
                                                        Products
                                                    </div>
                                                    <div style="margin-top:8px;">
                                                        @forelse ($products as $product)
                                                            <span style="display:inline-block;background:#eef2ff;border:1px solid #dbeafe;color:#1e293b;font-size:13px;font-weight:800;padding:6px 10px;border-radius:999px;margin-right:8px;margin-bottom:6px;">
                                                                {{ $product }}
                                                            </span>
                                                        @empty
                                                            <span style="color:#475569;font-size:14px;font-weight:700;">
                                                                No products assigned.
                                                            </span>
                                                        @endforelse
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td style="background:#fbfdff;border:1px solid #e6eefc;border-radius:14px;padding:14px 14px;">
                                                    <div style="font-size:12px;letter-spacing:0.4px;text-transform:uppercase;color:#64748b;font-weight:700;">
                                                        Stakeholders
                                                    </div>
                                                    <div style="margin-top:8px;color:#0f172a;font-size:14px;line-height:1.6;font-weight:700;">
                                                        {{ $stakeholders->isNotEmpty() ? $stakeholders->implode(', ') : 'None' }}
                                                    </div>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td style="background:#fff7ed;border:1px solid #fed7aa;border-radius:14px;padding:14px 14px;">
                                                    <div style="font-size:12px;letter-spacing:0.4px;text-transform:uppercase;color:#9a3412;font-weight:900;">
                                                        Notes
                                                    </div>
                                                    <div style="margin-top:8px;color:#7c2d12;font-size:14px;line-height:1.6;font-weight:800;">
                                                        {!! nl2br(e($kickoff->notes ?: 'No notes provided.')) !!}
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding:10px 18px 18px 18px;">
                                        <div style="height:1px;background:#e6eefc;margin:10px 0 16px 0;"></div>

                                        <div style="color:#0f172a;font-size:14px;line-height:1.7;">
                                            Best Regards,<br><br>

                                            <strong style="font-size:15px;">Viral Parmar</strong><br>
                                            <span style="color:#334155;font-weight:700;">Product Owner | FiscalOx</span><br>
                                            <span style="color:#334155;">Email:</span> <a href="mailto:Viral@fiscalox.com" style="color:#1d4ed8;text-decoration:none;font-weight:800;">Viral@fiscalox.com</a><br>
                                            <span style="color:#334155;">Phone:</span> <a href="tel:+916351493983" style="color:#1d4ed8;text-decoration:none;font-weight:800;">+91 6351493983</a>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td style="background:#0b1220;padding:16px 18px;color:#cbd5e1;font-size:12px;line-height:1.6;">
                                        (c) 2025 FiscalOx | All Rights Reserved<br>
                                        <a href="https://www.fiscalox.com" style="color:#93c5fd;text-decoration:none;font-weight:800;">www.fiscalox.com</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="height:14px;line-height:14px;font-size:0;">&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
