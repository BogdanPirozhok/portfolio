<?php

namespace App\Models\Cabinet;

use App\Contracts\Invitable;
use App\Contracts\Permissionable;
use App\Models\Account\Account;
use App\Models\Cabinet\ACL\CabinetPermission;
use App\Models\Cabinet\ACL\CabinetRole;
use App\Models\Contact\Contact;
use App\Models\Contact\ContactList;
use App\Models\Course\Course;
use App\Models\Email\EmailTemplate;
use App\Models\Import\ImportedDocument;
use App\Models\Invite\Invite;
use App\Models\Payment\Offer\Offer;
use App\Models\Payment\PaymentMethod\PaymentMethod;
use App\Models\Product\Product;
use App\Models\Tariff\Tariff;
use App\Models\Tariff\TariffSubscription;
use App\Models\User\User;
use App\Models\Webinar\Webinar;
use App\Services\Cabinet\ACL\CabinetAccessService;
use App\Services\System\RouteService;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class Cabinet.
 *
 * @package namespace App\Models\Cabinet;
 *
 * @property int $id
 * @property ?int $account_id
 * @property ?int $parent_id
 * @property int $user_id
 * @property int $type_id
 * @property ?int $tariff_id
 * @property bool $is_expert
 * @property bool $is_active
 * @property string $caption
 * @property string $description
 * @property bool $is_api_enabled
 * @property string $hash
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 *
 * @property string $link
 * @property string $anchor_title
 * @property string $anchor_link
 * @property bool $has_marketing_settings
 *
 * @property Account $account
 * @property Cabinet $parent
 * @property Collection|Cabinet[] $children
 * @property null|TariffSubscription currentTariffSubscription
 * @property Collection|TariffSubscription[] tariffSubscriptions
 * @property ?Tariff tariff
 * @property Collection|CabinetRole[] $cabinetRoles
 * @property CabinetRole[]|Collection $roles
 * @property CabinetPermission $permission
 * @property User $owner
 * @property null|Collection|User[] $users
 * @property null|Collection|Contact[] $contacts
 * @property null|Collection|Product[] $products
 * @property null|Collection|Course[] $courses
 * @property null|Collection|Offer[] $offers
 * @property CabinetType $type
 * @property null|Collection|ImportedDocument[] $importedDocuments
 * @property ?CabinetAnchor $anchor
 * @property ?CabinetMarketingSetting $marketingSettings
 * @property ?CabinetEmailSetting $emailSettings
 * @property null|Collection|EmailTemplate[] $emailTemplates
 * @property null|Collection|ContactList[] $contactLists
 * @property CabinetPersonalSetting $personalSettings
 * @property Collection|PaymentMethod[] $paymentMethod
 * @property PaymentMethod $balancePaymentMethod
 * @property PaymentMethod $defaultPaymentMethod
 * @property null|Collection|CabinetSite[] $sites
 * @property null|Collection|CabinetStatus[] $statuses
 */
class Cabinet extends Model implements Transformable, Invitable, Permissionable
{
    use TransformableTrait,
        SoftDeletes,
        LogsActivity;

    final const DEFAULT_MODERATOR_STAGE = 'mirius';
    final const DEFAULT_MODERATOR_PROD = 'karabas';
    final const CABINET_SITES_LIMIT = 10;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id',
        'parent_id',
        'user_id',
        'type_id',
        'is_expert',
        'is_active',
        'is_api_enabled',
        'caption',
        'description',
        'hash'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_expert' => 'boolean',
        'is_api_enabled' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logFillable()->logOnlyDirty();
    }

    public function getCabinetId(): int
    {
        return $this->id;
    }

    public function getInviteName(): string
    {
        return lang('event.invites.cabinet');
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Cabinet::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(Cabinet::class, 'parent_id', 'id');
    }

    public function currentTariffSubscription(): HasOne
    {
        return $this->hasOne(TariffSubscription::class)->ofMany();
    }

    public function tariffSubscriptions(): HasMany
    {
        return $this->hasMany(TariffSubscription::class);
    }

    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class);
    }

    public function cabinetRoles(): MorphToMany
    {
        return $this->morphToMany(CabinetRole::class, 'roleable', 'cabinet_roleables')
            ->withTimestamps();
    }

    public function permission(): MorphOne
    {
        return $this->morphOne(CabinetPermission::class, 'permissionable');
    }

    public function sites(): HasMany
    {
        return $this->hasMany(CabinetSite::class)
            ->orderBy('id', 'desc');
    }

    public function getDefaultSite(): CabinetSite
    {
        $tag = RouteService::CABINET_CACHE_TAG . '.' . $this->id;
        $cacheKeySite = RouteService::CABINET_CACHE_KEY . $this->id . '.site';

        if (!($site = Cache::tags($tag)->get($cacheKeySite))) {
            $site = $this->sites()
                ->where('is_default', '=', true)
                ->first();

            Cache::tags($tag)->set($cacheKeySite, $site, CarbonInterface::SECONDS_PER_MINUTE * 5);
        }

        return $site;
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(CabinetStatus::class);
    }

    public function getUsedSite(): CabinetSite
    {
        /** @var CabinetSite $requestSite */
        $requestSite = cabinetSite();
        $defaultSite = $this->getDefaultSite();

        if ($requestSite
            && $requestSite->cabinet_id === $defaultSite->cabinet_id
            && $requestSite->id !== $defaultSite->id
        ) {
            return $requestSite;
        }

        return $defaultSite;
    }

    public function cabinetSystemRoles(): MorphToMany
    {
        return $this->cabinetRoles()->where('type_id', CabinetRole::TYPE_SYSTEM);
    }

    public function cabinetServiceRoles(): MorphToMany
    {
        return $this->cabinetRoles()->where('type_id', CabinetRole::TYPE_SERVICE);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(CabinetUser::class)
            ->wherePivotNull('deleted_at')
            ->withPivot(['is_blocked'])
            ->withTimestamps();
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class);
    }

    public function blockedContacts(): HasMany
    {
        return $this->hasMany(Contact::class)->whereNotNull('blocked_at');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(CabinetType::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Offer::class);
    }

    public function webinars(): HasMany
    {
        return $this->hasMany(Webinar::class);
    }

    public function importedDocuments(): HasMany
    {
        return $this->hasMany(ImportedDocument::class);
    }

    public function roles(): HasMany
    {
        return $this->hasMany(CabinetRole::class);
    }

    public function invites(): MorphMany
    {
        return $this->morphMany(Invite::class, 'invitable');
    }

    public function anchor(): HasOne
    {
        return $this->hasOne(CabinetAnchor::class);
    }

    public function marketingSettings(): HasOne
    {
        return $this->hasOne(CabinetMarketingSetting::class);
    }

    public function emailSettings(): HasOne
    {
        return $this->hasOne(CabinetEmailSetting::class);
    }

    public function emailTemplates(): HasMany
    {
        return $this->hasMany(EmailTemplate::class)->where('type_id', EmailTemplate::TYPE_CUSTOM)->orderBy('id', 'desc');
    }

    public function contactLists(): HasMany
    {
        return $this->hasMany(ContactList::class);
    }

    public function personalSettings(): HasOne
    {
        return $this->hasOne(CabinetPersonalSetting::class);
    }

    public function paymentMethods(): HasMany
    {
        return $this->hasMany(PaymentMethod::class);
    }

    public function balancePaymentMethod(): HasOne
    {
        return $this->hasOne(PaymentMethod::class)->ofMany(['id' => 'max'], function ($query) {
            $query->where('type_id', PaymentMethod::TYPE_BALANCE);
        });
    }

    public function defaultPaymentMethod(): HasOne
    {
        return $this->hasOne(PaymentMethod::class)->ofMany(['id' => 'max'], function ($query) {
            $query->where('is_default', true);
        });
    }

    public function scopeHasPermission(Builder $query, string $permission): Builder
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user) {
            $cabinet = cabinet();

            /** @var CabinetAccessService $accessService */
            $accessService = resolve(CabinetAccessService::class);
            $isOwner = $accessService->userIsOwner($cabinet->id, $user->id);

            if (!$isOwner) {
                $userPermissions = $accessService->getUserPermissions($cabinet->id, $user->id);

                return $query->whereIn(
                    DB::raw('CONCAT("' . $permission . '", ".", id)'),
                    $userPermissions
                );
            }
        }

        return $query;
    }

    public function getLinkAttribute(): string
    {
        $subdomain = $this->getUsedSite()->slug;
        $domain = config('app.domain');
        $link = str_replace($domain, $subdomain . '.' . $domain, config('app.url'));

        return str($link)->finish('/');
    }

    public function getAnchorTitleAttribute(): string
    {
        $anchor = $this->anchor;

        return $anchor ? $anchor->title : lang('footer.anchor_default');
    }

    public function getAnchorLinkAttribute(): string
    {
        return currentLocaleRoute('home');
    }

    public function getHasMarketingSettingsAttribute(): bool
    {
        return $this->marketingSettings
            && !empty($this->marketingSettings->company_name)
            && !empty($this->marketingSettings->address);
    }

    public function getMainCabinet(): Cabinet
    {
        return $this->isParent() ? $this : $this->parent;
    }

    public function isParent(): bool
    {
        return is_null($this->parent_id);
    }

    public function isAvailableSitesLimit(): bool
    {
        return $this->sites->count() < self::CABINET_SITES_LIMIT;
    }

    /**
     * The attribute "name" may be present in the model, in older versions
     */
    public function getTitle(): string
    {
        return $this->getUsedSite()->name;
    }

    /**
     * The attribute "slug" may be present in the model, in older versions
     */
    public function getSlug(): string
    {
        return $this->getUsedSite()->slug;
    }

    public function getDomain(): string
    {
        $domain = $this->getUsedSite()->activeDomain;

        if ($domain) {
            return $domain->hostname;
        }
        return $this->getSlug() . '.' . config('app.domain');
    }

    public function getUsedDomain(): ?string
    {
        $domain = $this->getUsedSite()->activeDomain;

        if ($domain) {
            return $domain->hostname;
        }
        return null;
    }

    public function getUniqId(): string
    {
        return $this->getInstanceKey() . '.' . $this->getKey();
    }

    public function getInstanceKey(): string
    {
        return 'cabinet';
    }
}
