<?php
// [file name]: User.php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'opd_id',
        'avatar',
        'last_login_at',
        'is_active',
        'status_verifikasi',
        'rejection_reason',
        'rejected_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'rejected_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    protected $appends = [
        'role_label',
        'role_badge_class',
        'avatar_url',
        'opd_name',
        'status_verifikasi_label',
        'status_verifikasi_badge_class',
        'rejected_at_formatted',
        'rejection_reason_formatted',
        'verification_status_options'
    ];

    // Role constants
    const ROLE_ADMIN = 'admin';
    const ROLE_EXECUTIVE = 'executive';
    const ROLE_VERIFICATOR = 'verificator';
    const ROLE_UPLOADER = 'uploader';
    const ROLE_VIEWER = 'viewer';

    // Status verifikasi constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // Relationship dengan OPD
    public function opd()
    {
        return $this->belongsTo(Opd::class);
    }

    // Accessor untuk nama OPD
    public function getOpdNameAttribute()
    {
        return $this->opd ? $this->opd->name : '-';
    }

    // Role methods
    public function isAdmin()
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isExecutive()
    {
        return $this->role === self::ROLE_EXECUTIVE;
    }

    public function isVerificator()
    {
        return $this->role === self::ROLE_VERIFICATOR;
    }

    public function isUploader()
    {
        return $this->role === self::ROLE_UPLOADER;
    }

    public function isViewer()
    {
        return $this->role === self::ROLE_VIEWER;
    }

    public function getRoleLabelAttribute()
    {
        $roles = [
            self::ROLE_ADMIN => 'Administrator',
            self::ROLE_EXECUTIVE => 'Eksekutif',
            self::ROLE_VERIFICATOR => 'Verifikator',
            self::ROLE_UPLOADER => 'Uploader',
            self::ROLE_VIEWER => 'Viewer'
        ];

        return $roles[$this->role] ?? 'Unknown';
    }

    public function getRoleBadgeClassAttribute()
    {
        $classes = [
            self::ROLE_ADMIN => 'bg-danger',
            self::ROLE_EXECUTIVE => 'bg-primary',
            self::ROLE_VERIFICATOR => 'bg-warning',
            self::ROLE_UPLOADER => 'bg-success',
            self::ROLE_VIEWER => 'bg-info'
        ];

        return $classes[$this->role] ?? 'bg-secondary';
    }

    // Accessor untuk avatar
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/avatars/' . $this->avatar);
        }

        $name = urlencode($this->name);
        $backgroundColors = [
            'admin' => 'FF6B6B',
            'executive' => '4ECDC4',
            'verificator' => '45B7D1',
            'uploader' => '96CEB4',
            'viewer' => '6C757D'
        ];

        $bgColor = $backgroundColors[$this->role] ?? '7367F0';
        return "https://ui-avatars.com/api/?name={$name}&background={$bgColor}&color=fff&size=150&bold=true&font-size=0.5";
    }

    // Status verifikasi methods
    public function getStatusVerifikasiLabelAttribute()
    {
        $statuses = [
            self::STATUS_PENDING => 'Menunggu Verifikasi',
            self::STATUS_APPROVED => 'Terverifikasi',
            self::STATUS_REJECTED => 'Ditolak'
        ];

        return $statuses[$this->status_verifikasi] ?? 'Unknown';
    }

    public function getStatusVerifikasiBadgeClassAttribute()
    {
        $classes = [
            self::STATUS_PENDING => 'bg-warning',
            self::STATUS_APPROVED => 'bg-success',
            self::STATUS_REJECTED => 'bg-danger'
        ];

        return $classes[$this->status_verifikasi] ?? 'bg-secondary';
    }

    public function getRejectedAtFormattedAttribute()
    {
        return $this->rejected_at ? $this->rejected_at->translatedFormat('d F Y H:i') : '-';
    }

    public function getRejectionReasonFormattedAttribute()
    {
        if ($this->rejection_reason) {
            return $this->rejection_reason;
        }

        return 'Akun Anda ditolak oleh administrator. Silakan hubungi administrator untuk informasi lebih lanjut.';
    }

    public function getVerificationStatusOptionsAttribute()
    {
        return [
            self::STATUS_PENDING => 'Menunggu Verifikasi',
            self::STATUS_APPROVED => 'Terverifikasi',
            self::STATUS_REJECTED => 'Ditolak'
        ];
    }

    // Scope untuk filter
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopePendingVerification($query)
    {
        return $query->where('status_verifikasi', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status_verifikasi', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status_verifikasi', self::STATUS_REJECTED);
    }

    public function scopeNeedsVerification($query)
    {
        return $query->where('role', self::ROLE_VIEWER)
                    ->whereIn('status_verifikasi', [self::STATUS_PENDING, self::STATUS_REJECTED]);
    }

    // Status check methods - PERBAIKAN: Hanya gunakan 3 method ini
    public function isVerified()
    {
        return $this->status_verifikasi === self::STATUS_APPROVED;
    }

    public function isPending()
    {
        return $this->status_verifikasi === self::STATUS_PENDING;
    }

    public function isRejected()
    {
        return $this->status_verifikasi === self::STATUS_REJECTED;
    }

    /**
     * Method untuk mengubah status verifikasi
     */
    public function updateVerificationStatus($status, $rejectionReason = null)
    {
        $updates = [
            'status_verifikasi' => $status,
            'rejected_at' => null,
            'rejection_reason' => null
        ];

        if ($status === self::STATUS_APPROVED) {
            $updates['is_active'] = true;
        } elseif ($status === self::STATUS_REJECTED) {
            $updates['is_active'] = false;
            $updates['rejected_at'] = now();
            $updates['rejection_reason'] = $rejectionReason;
        } elseif ($status === self::STATUS_PENDING) {
            $updates['is_active'] = false;
        }

        return $this->update($updates);
    }

    // Relationship dengan dokumen yang diverifikasi
    public function verifiedDocuments()
    {
        return $this->hasMany(JenisDokumen::class, 'verified_by');
    }

    // Method untuk statistik
    public function getVerifiedDocumentsCountAttribute()
    {
        return $this->verifiedDocuments()->count();
    }

    public function getRecentActivityAttribute()
    {
        return $this->verifiedDocuments()
            ->with('project')
            ->latest()
            ->take(5)
            ->get();
    }

    // Menu items berdasarkan role
    public function getMenuItemsAttribute()
    {
        $baseMenu = [
            [
                'name' => 'Dashboard',
                'route' => 'dashboard',
                'icon' => 'fas fa-tachometer-alt',
                'active' => request()->routeIs('dashboard')
            ]
        ];

        $roleMenus = [
            self::ROLE_ADMIN => [
                [
                    'name' => 'OPD',
                    'route' => 'opds.index',
                    'icon' => 'fas fa-building',
                    'active' => request()->routeIs('opds.*')
                ],
                [
                    'name' => 'Developers',
                    'route' => 'developers.index',
                    'icon' => 'fas fa-code-branch',
                    'active' => request()->routeIs('developers.*')
                ],
                [
                    'name' => 'Tahapan',
                    'route' => 'tahapans.index',
                    'icon' => 'fas fa-list-ol',
                    'active' => request()->routeIs('tahapans.*')
                ],
                [
                    'name' => 'Projects',
                    'route' => 'projects.index',
                    'icon' => 'fas fa-project-diagram',
                    'active' => request()->routeIs('projects.*')
                ],
                [
                    'name' => 'Project Timeline',
                    'route' => 'projects.timeline.global',
                    'icon' => 'fas fa-stream',
                    'active' => request()->routeIs('projects.timeline.*')
                ],
                [
                    'name' => 'Dokumen',
                    'route' => 'jenis-dokumen.index',
                    'icon' => 'fas fa-file-alt',
                    'active' => request()->routeIs('jenis-dokumen.*')
                ],
                [
                    'name' => 'Users',
                    'route' => 'users.index',
                    'icon' => 'fas fa-users',
                    'active' => request()->routeIs('users.*')
                ]
            ],
            self::ROLE_EXECUTIVE => [
                [
                    'name' => 'Projects',
                    'route' => 'projects.index',
                    'icon' => 'fas fa-project-diagram',
                    'active' => request()->routeIs('projects.*')
                ],
                [
                    'name' => 'Project Timeline',
                    'route' => 'projects.timeline.global',
                    'icon' => 'fas fa-stream',
                    'active' => request()->routeIs('projects.timeline.*')
                ],
                [
                    'name' => 'Dokumen',
                    'route' => 'jenis-dokumen.index',
                    'icon' => 'fas fa-file-alt',
                    'active' => request()->routeIs('jenis-dokumen.*')
                ]
            ],
            self::ROLE_VERIFICATOR => [
                [
                    'name' => 'Projects',
                    'route' => 'projects.index',
                    'icon' => 'fas fa-project-diagram',
                    'active' => request()->routeIs('projects.*')
                ],
                [
                    'name' => 'Project Timeline',
                    'route' => 'projects.timeline.global',
                    'icon' => 'fas fa-stream',
                    'active' => request()->routeIs('projects.timeline.*')
                ],
                [
                    'name' => 'Dokumen',
                    'route' => 'jenis-dokumen.index',
                    'icon' => 'fas fa-file-alt',
                    'active' => request()->routeIs('jenis-dokumen.*')
                ]
            ],
            self::ROLE_UPLOADER => [
                [
                    'name' => 'Projects',
                    'route' => 'projects.index',
                    'icon' => 'fas fa-project-diagram',
                    'active' => request()->routeIs('projects.*')
                ],
                [
                    'name' => 'Project Timeline',
                    'route' => 'projects.timeline.global',
                    'icon' => 'fas fa-stream',
                    'active' => request()->routeIs('projects.timeline.*')
                ],
                [
                    'name' => 'Dokumen',
                    'route' => 'jenis-dokumen.index',
                    'icon' => 'fas fa-file-alt',
                    'active' => request()->routeIs('jenis-dokumen.*')
                ]
            ],
            self::ROLE_VIEWER => [
                [
                    'name' => 'Project Timeline',
                    'route' => 'projects.timeline.global',
                    'icon' => 'fas fa-stream',
                    'active' => request()->routeIs('projects.timeline.*')
                ]
            ]
        ];

        return array_merge($baseMenu, $roleMenus[$this->role] ?? []);
    }

    public function canAccessMenu($menuRoute)
    {
        $menuItems = $this->menu_items;

        foreach ($menuItems as $menu) {
            if ($menu['route'] === $menuRoute) {
                return true;
            }
        }

        return false;
    }
}
