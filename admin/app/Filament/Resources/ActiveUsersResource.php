<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActiveUsersResource\Pages;
use App\Models\Membership;
use App\Models\Plan;
use App\Models\PlanCategory;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use App\Filament\Actions\AssignPlanAction;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;

class ActiveUsersResource extends Resource
{
    protected static ?string $model = Membership::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'User Management';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Active Members';
    protected static ?string $slug = 'active-members';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereSubscriptionCurrent();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('email')->email()->required()->maxLength(255),
                TextInput::make('phone')->tel()->maxLength(191)->nullable(),

                Select::make('country')->options([
                    "" => "Select Country",
                    "Afghanistan" => "Afghanistan",
                    "Albania" => "Albania",
                    "Algeria" => "Algeria",
                    "American Samoa" => "American Samoa",
                    "Andorra" => "Andorra",
                    "Angola" => "Angola",
                    "Anguilla" => "Anguilla",
                    "Antartica" => "Antarctica",
                    "Antigua and Barbuda" => "Antigua and Barbuda",
                    "Argentina" => "Argentina",
                    "Armenia" => "Armenia",
                    "Aruba" => "Aruba",
                    "Australia" => "Australia",
                    "Austria" => "Austria",
                    "Azerbaijan" => "Azerbaijan",
                    "Bahamas" => "Bahamas",
                    "Bahrain" => "Bahrain",
                    "Bangladesh" => "Bangladesh",
                    "Barbados" => "Barbados",
                    "Belarus" => "Belarus",
                    "Belgium" => "Belgium",
                    "Belize" => "Belize",
                    "Benin" => "Benin",
                    "Bermuda" => "Bermuda",
                    "Bhutan" => "Bhutan",
                    "Bolivia" => "Bolivia",
                    "Bosnia and Herzegowina" => "Bosnia and Herzegowina",
                    "Botswana" => "Botswana",
                    "Bouvet Island" => "Bouvet Island",
                    "Brazil" => "Brazil",
                    "British Indian Ocean Territory" => "British Indian Ocean Territory",
                    "Brunei Darussalam" => "Brunei Darussalam",
                    "Bulgaria" => "Bulgaria",
                    "Burkina Faso" => "Burkina Faso",
                    "Burundi" => "Burundi",
                    "Cambodia" => "Cambodia",
                    "Cameroon" => "Cameroon",
                    "Canada" => "Canada",
                    "Cape Verde" => "Cape Verde",
                    "Cayman Islands" => "Cayman Islands",
                    "Central African Republic" => "Central African Republic",
                    "Chad" => "Chad",
                    "Chile" => "Chile",
                    "China" => "China",
                    "Christmas Island" => "Christmas Island",
                    "Cocos Islands" => "Cocos (Keeling) Islands",
                    "Colombia" => "Colombia",
                    "Comoros" => "Comoros",
                    "Congo" => "Congo",
                    "Congo, the Democratic Republic of the" => "Congo, the Democratic Republic of the",
                    "Cook Islands" => "Cook Islands",
                    "Costa Rica" => "Costa Rica",
                    "Cota D'Ivoire" => "Cote d'Ivoire",
                    "Croatia" => "Croatia (Hrvatska)",
                    "Cuba" => "Cuba",
                    "Cyprus" => "Cyprus",
                    "Czech Republic" => "Czech Republic",
                    "Denmark" => "Denmark",
                    "Djibouti" => "Djibouti",
                    "Dominica" => "Dominica",
                    "Dominican Republic" => "Dominican Republic",
                    "East Timor" => "East Timor",
                    "Ecuador" => "Ecuador",
                    "Egypt" => "Egypt",
                    "El Salvador" => "El Salvador",
                    "Equatorial Guinea" => "Equatorial Guinea",
                    "Eritrea" => "Eritrea",
                    "Estonia" => "Estonia",
                    "Ethiopia" => "Ethiopia",
                    "Falkland Islands" => "Falkland Islands (Malvinas)",
                    "Faroe Islands" => "Faroe Islands",
                    "Fiji" => "Fiji",
                    "Finland" => "Finland",
                    "France" => "France",
                    "France Metropolitan" => "France, Metropolitan",
                    "French Guiana" => "French Guiana",
                    "French Polynesia" => "French Polynesia",
                    "French Southern Territories" => "French Southern Territories",
                    "Gabon" => "Gabon",
                    "Gambia" => "Gambia",
                    "Georgia" => "Georgia",
                    "Germany" => "Germany",
                    "Ghana" => "Ghana",
                    "Gibraltar" => "Gibraltar",
                    "Greece" => "Greece",
                    "Greenland" => "Greenland",
                    "Grenada" => "Grenada",
                    "Guadeloupe" => "Guadeloupe",
                    "Guam" => "Guam",
                    "Guatemala" => "Guatemala",
                    "Guinea" => "Guinea",
                    "Guinea-Bissau" => "Guinea-Bissau",
                    "Guyana" => "Guyana",
                    "Haiti" => "Haiti",
                    "Heard and McDonald Islands" => "Heard and Mc Donald Islands",
                    "Holy See" => "Holy See (Vatican City State)",
                    "Honduras" => "Honduras",
                    "Hong Kong" => "Hong Kong",
                    "Hungary" => "Hungary",
                    "Iceland" => "Iceland",
                    "India" => "India",
                    "Indonesia" => "Indonesia",
                    "Iran" => "Iran (Islamic Republic of)",
                    "Iraq" => "Iraq",
                    "Ireland" => "Ireland",
                    "Israel" => "Israel",
                    "Italy" => "Italy",
                    "Jamaica" => "Jamaica",
                    "Japan" => "Japan",
                    "Jordan" => "Jordan",
                    "Kazakhstan" => "Kazakhstan",
                    "Kenya" => "Kenya",
                    "Kiribati" => "Kiribati",
                    "Democratic People's Republic of Korea" => "Korea, Democratic People's Republic of",
                    "Korea" => "Korea, Republic of",
                    "Kuwait" => "Kuwait",
                    "Kyrgyzstan" => "Kyrgyzstan",
                    "Lao" => "Lao People's Democratic Republic",
                    "Latvia" => "Latvia",
                    "Lebanon" => "Lebanon",
                    "Lesotho" => "Lesotho",
                    "Liberia" => "Liberia",
                    "Libyan Arab Jamahiriya" => "Libyan Arab Jamahiriya",
                    "Liechtenstein" => "Liechtenstein",
                    "Lithuania" => "Lithuania",
                    "Luxembourg" => "Luxembourg",
                    "Macau" => "Macau",
                    "Macedonia" => "Macedonia, The Former Yugoslav Republic of",
                    "Madagascar" => "Madagascar",
                    "Malawi" => "Malawi",
                    "Malaysia" => "Malaysia",
                    "Maldives" => "Maldives",
                    "Mali" => "Mali",
                    "Malta" => "Malta",
                    "Marshall Islands" => "Marshall Islands",
                    "Martinique" => "Martinique",
                    "Mauritania" => "Mauritania",
                    "Mauritius" => "Mauritius",
                    "Mayotte" => "Mayotte",
                    "Mexico" => "Mexico",
                    "Micronesia" => "Micronesia, Federated States of",
                    "Moldova" => "Moldova, Republic of",
                    "Monaco" => "Monaco",
                    "Mongolia" => "Mongolia",
                    "Montserrat" => "Montserrat",
                    "Morocco" => "Morocco",
                    "Mozambique" => "Mozambique",
                    "Myanmar" => "Myanmar",
                    "Namibia" => "Namibia",
                    "Nauru" => "Nauru",
                    "Nepal" => "Nepal",
                    "Netherlands" => "Netherlands",
                    "Netherlands Antilles" => "Netherlands Antilles",
                    "New Caledonia" => "New Caledonia",
                    "New Zealand" => "New Zealand",
                    "Nicaragua" => "Nicaragua",
                    "Niger" => "Niger",
                    "Nigeria" => "Nigeria",
                    "Niue" => "Niue",
                    "Norfolk Island" => "Norfolk Island",
                    "Northern Mariana Islands" => "Northern Mariana Islands",
                    "Norway" => "Norway",
                    "Oman" => "Oman",
                    "Pakistan" => "Pakistan",
                    "Palau" => "Palau",
                    "Panama" => "Panama",
                    "Papua New Guinea" => "Papua New Guinea",
                    "Paraguay" => "Paraguay",
                    "Peru" => "Peru",
                    "Philippines" => "Philippines",
                    "Pitcairn" => "Pitcairn",
                    "Poland" => "Poland",
                    "Portugal" => "Portugal",
                    "Puerto Rico" => "Puerto Rico",
                    "Qatar" => "Qatar",
                    "Reunion" => "Reunion",
                    "Romania" => "Romania",
                    "Russia" => "Russian Federation",
                    "Rwanda" => "Rwanda",
                    "Saint Kitts and Nevis" => "Saint Kitts and Nevis",
                    "Saint LUCIA" => "Saint LUCIA",
                    "Saint Vincent" => "Saint Vincent and the Grenadines",
                    "Samoa" => "Samoa",
                    "San Marino" => "San Marino",
                    "Sao Tome and Principe" => "Sao Tome and Principe",
                    "Saudi Arabia" => "Saudi Arabia",
                    "Senegal" => "Senegal",
                    "Seychelles" => "Seychelles",
                    "Sierra" => "Sierra Leone",
                    "Singapore" => "Singapore",
                    "Slovakia" => "Slovakia (Slovak Republic)",
                    "Slovenia" => "Slovenia",
                    "Solomon Islands" => "Solomon Islands",
                    "Somalia" => "Somalia",
                    "South Africa" => "South Africa",
                    "South Georgia" => "South Georgia and the South Sandwich Islands",
                    "Span" => "Spain",
                    "SriLanka" => "Sri Lanka",
                    "St. Helena" => "St. Helena",
                    "St. Pierre and Miguelon" => "St. Pierre and Miquelon",
                    "Sudan" => "Sudan",
                    "Suriname" => "Suriname",
                    "Svalbard" => "Svalbard and Jan Mayen Islands",
                    "Swaziland" => "Swaziland",
                    "Sweden" => "Sweden",
                    "Switzerland" => "Switzerland",
                    "Syria" => "Syrian Arab Republic",
                    "Taiwan" => "Taiwan, Province of China",
                    "Tajikistan" => "Tajikistan",
                    "Tanzania" => "Tanzania, United Republic of",
                    "Thailand" => "Thailand",
                    "Togo" => "Togo",
                    "Tokelau" => "Tokelau",
                    "Tonga" => "Tonga",
                    "Trinidad and Tobago" => "Trinidad and Tobago",
                    "Tunisia" => "Tunisia",
                    "Turkey" => "Turkey",
                    "Turkmenistan" => "Turkmenistan",
                    "Turks and Caicos" => "Turks and Caicos Islands",
                    "Tuvalu" => "Tuvalu",
                    "Uganda" => "Uganda",
                    "Ukraine" => "Ukraine",
                    "United Arab Emirates" => "United Arab Emirates",
                    "United Kingdom" => "United Kingdom",
                    "United States" => "United States",
                    "United States Minor Outlying Islands" => "United States Minor Outlying Islands",
                    "Uruguay" => "Uruguay",
                    "Uzbekistan" => "Uzbekistan",
                    "Vanuatu" => "Vanuatu",
                    "Venezuela" => "Venezuela",
                    "Vietnam" => "Viet Nam",
                    "Virgin Islands (British)" => "Virgin Islands (British)",
                    "Virgin Islands (U.S)" => "Virgin Islands (U.S.)",
                    "Wallis and Futana Islands" => "Wallis and Futuna Islands",
                    "Western Sahara" => "Western Sahara",
                    "Yemen" => "Yemen",
                    "Serbia" => "Serbia",
                    "Zambia" => "Zambia",
                    "Zimbabwe" => "Zimbabwe"
                ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('phone')->searchable()->toggleable(),
                BadgeColumn::make('subscription_status')
                    ->label('Status')
                    ->getStateUsing(function ($record) {
                        // Active only when next_due_date exists and is later than now
                        if (empty($record->next_due_date)) {
                            return 'Inactive';
                        }

                        try {
                            $due = Carbon::createFromFormat('Y-m-d H:i:s', $record->next_due_date);
                        } catch (Exception $e) {
                            // If parsing fails, treat as inactive
                            return 'Inactive';
                        }

                        return $due->greaterThan(Carbon::now()) ? 'Active' : 'Inactive';
                    })
                    ->colors([
                        'success' => 'Active',
                        'danger' => 'Inactive',
                    ]),
                TextColumn::make('subscription_id')
                    ->label('Plan')
                    ->html()
                    ->getStateUsing(function ($record) {
                        $planName = optional(\App\Models\PlanCategory::find($record->subscription_id))->name;

                        if (! $planName) {
                            return '-';
                        }

                        $subDate = $record->sub_date ?? null;
                        $nextDue = $record->next_due_date ?? null;

                        // Build HTML: bold plan name on top
                        // Also compute and show the duration between sub_date and next_due_date (e.g. "1 week")
                        $durationLabel = null;

                        if ($subDate && $nextDue) {
                            try {
                                $start = Carbon::createFromFormat('Y-m-d H:i:s', $subDate);
                                $end = Carbon::createFromFormat('Y-m-d H:i:s', $nextDue);
                                // diffForHumans with $absolute = true returns strings like "1 week", "2 days"
                                $durationLabel = $start->diffForHumans($end, true);
                            } catch (Exception $e) {
                                // ignore parsing errors and leave durationLabel null
                                $durationLabel = null;
                            }
                        }

                        $html = '<div><strong>' . e($planName) . '</strong>';

                        if ($durationLabel) {
                            $html .= ' <span class="text-sm text-gray-500">(' . e($durationLabel) . ')</span>';
                        }

                        if ($subDate) {
                            $html .= '<div class="text-sm text-gray-500">Subscribed: ' . e($subDate) . '</div>';
                        }

                        if ($nextDue) {
                            $html .= '<div class="text-sm text-gray-500">Expiry: ' . e($nextDue) . '</div>';
                        }

                        $html .= '</div>';

                        return $html;
                    }),
                TextColumn::make('country'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Action::make('updatePassword')
                        ->label('Update Password')
                        ->icon('heroicon-o-key')
                        ->color('warning')
                        ->form([
                            TextInput::make('password')->password()->required()->minLength(6),
                            TextInput::make('password_confirm')->password()->required()->minLength(6),
                        ])
                        ->action(function (Membership $record, array $data) {
                            if ($data['password'] !== $data['password_confirm']) {
                                throw new \Exception('Passwords do not match');
                            }
                            $record->password = Hash::make($data['password']);
                            $record->save();
                            \Filament\Notifications\Notification::make()->title('Password updated')->success()->send();
                        }),

                    AssignPlanAction::make(),

                    Action::make('unsubscribe')
                        ->label('Unsubscribe')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function (Membership $record) {
                            // Load subscriptions (newest first)
                            $subscriptions = \App\Models\MemberSubscription::where('user_id', $record->id)
                                ->orderBy('id', 'desc')
                                ->get();

                            if ($subscriptions->isEmpty()) {
                                \Filament\Notifications\Notification::make()
                                    ->title('No subscriptions found')
                                    ->warning()
                                    ->send();
                                return;
                            }

                            // Delete the latest (current) subscription
                            $latest = $subscriptions->first();
                            $latest->delete();

                            // Reload remaining subscriptions
                            $remaining = \App\Models\MemberSubscription::where('user_id', $record->id)
                                ->orderBy('id', 'desc')
                                ->get();

                            $now = Carbon::now();

                            // If there's a previous subscription and its next_due_date is in the future,
                            // roll the membership back to that plan
                            if ($remaining->isNotEmpty()) {
                                $prev = $remaining->first();
                                try {
                                    $prevDue = $prev->next_due_date ? Carbon::createFromFormat('Y-m-d H:i:s', $prev->next_due_date) : null;
                                } catch (Exception $e) {
                                    $prevDue = null;
                                }

                                if ($prevDue && $prevDue->greaterThan($now)) {
                                    $record->subscription_id = $prev->category_id;
                                    $record->subscription_status = 1;
                                    $record->sub_date = $prev->sub_date;
                                    $record->next_due_date = $prev->next_due_date;
                                    $record->save();

                                    \Filament\Notifications\Notification::make()
                                        ->title('Unsubscribed — rolled back to previous plan')
                                        ->success()
                                        ->send();

                                    return;
                                }
                            }

                            // If no previous active subscription, pick the next future subscription (if any)
                            $nextFuture = \App\Models\MemberSubscription::where('user_id', $record->id)
                                ->where('next_due_date', '>', $now->format('Y-m-d H:i:s'))
                                ->orderBy('next_due_date', 'asc')
                                ->first();

                            if ($nextFuture) {
                                // No active plan, but there's a scheduled upcoming subscription
                                $record->subscription_status = 0;
                                $record->sub_date = $nextFuture->sub_date;
                                $record->next_due_date = $nextFuture->next_due_date;
                                $record->save();

                                \Filament\Notifications\Notification::make()
                                    ->title('Unsubscribed — no active plan, next scheduled subscription preserved')
                                    ->success()
                                    ->send();

                                return;
                            }

                            // No subscriptions left at all
                            $record->subscription_status = 0;
                            $record->subscription_id = null;
                            $record->sub_date = null;
                            $record->next_due_date = null;
                            $record->save();

                            \Filament\Notifications\Notification::make()
                                ->title('Unsubscribed — no subscriptions remain')
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])
                    ->label('Actions')
                    ->icon('heroicon-o-bars-3'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActiveUsers::route('/'),
        ];
    }
}
