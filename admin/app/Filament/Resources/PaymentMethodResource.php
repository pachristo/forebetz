<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentMethodResource\Pages;
use App\Models\PaymentMethod;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Table as TablesTable;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class PaymentMethodResource extends Resource
{
    protected static ?string $model = PaymentMethod::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Payments';
    protected static ?string $navigationLabel = 'Payment Methods';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Grid::make(2)->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->columnSpanFull()
                            ->maxLength(255),

                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->directory('payment-methods')
                            ->disk('public')
                            ->visibility('public')
                            ->maxSize(15048)
                            ->columnSpanFull()
                            ->nullable()
                            ->helperText('Shown on the checkout payment card (stored in admin public storage).'),

                        Forms\Components\Select::make('country')->options([
                    "" => "Select Country",
                    //  "all" => "All countries",
                        "others" => "All USD countries",
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
                ])
                            ->placeholder('e.g. NG or United States')
                            ->columnSpanFull()
                            ->nullable(),
                    ]),
   TinyEditor::make('instructions')
                        ->label('Instructions')
                         ->placeholder('information on the payment and it details')
                        ->nullable(),
                    Forms\Components\Textarea::make('text')
                        ->label('Payment proof instruction')
                        ->rows(4)
                        ->columnSpanFull()
                        ->placeholder(
                            "After you're done with the payment, kindly send the payment proof to our email {contact_email} "
                            .'or WhatsApp phone number {whatsapp_no}. Your account will be activated instantly.'
                        )
                        ->helperText(
                            'Shown in the white footer on this payment card. '
                            .'Placeholders: {contact_email}, {whatsapp_no}, {contact_phone}. '
                            .'Leave empty to use the global instruction from Settings → Site Configuration.'
                        )
                        ->nullable(),


                ]),
            ]);
    }

    public static function table(TablesTable $table): TablesTable
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->square(),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('country')->label('Country'),
                Tables\Columns\TextColumn::make('created_at')->label('Created')->date(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentMethods::route('/'),
            'create' => Pages\CreatePaymentMethod::route('/create'),
            'edit' => Pages\EditPaymentMethod::route('/{record}/edit'),
        ];
    }
}
