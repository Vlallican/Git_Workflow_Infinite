<?php

namespace App\Models;

use App\Http\Requests\ContactRequest;
use Illuminate\Database\Eloquent\Model;
use Mailjet\LaravelMailjet\Facades\Mailjet;
use Mailjet\Resources;

class Contact extends Model
{
    /**
     * @var string
     */
    protected $table = 'contacts';

    /**
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array fillable
     */
    protected $fillable = [
        'id', 'ds_name', 'firstname', 'lastname', 'email', 'phone', 'nb_vehicle'
    ];

    protected $dates = [
        'created_at', 'updated_at'
    ];


    /**
     *  Création d'un contact via ContactRequest
     *
     * @param ContactRequest $request
     * @return $this
     */
    public function createContact(ContactRequest $request) : Contact {
        $this->ds_name = $request['ds_name'];
        $this->firstname = $request['firstname'];
        $this->lastname = $request['lastname'];
        $this->email = $request['email'];
        $this->phone = $request['phone'];
        $this->nb_vehicle = $request['nb_vehicle'];
        return $this;
    }

    /**
     *  Envoi d'un mail du coter de smartmoov ou l'assurance contenant les informations que le client à transmis pour un devis
     **/
    public function sendMailBack($contact){
        $mj = Mailjet::getClient();
        $body = [
            'FromEmail' => "contact@assurance-conduite.fr",
            'FromName' => "Assurance Conduite",
            'Subject' => "Nouvelle demande de devis",
            'MJ-TemplateID' => '1516763',
            'MJ-TemplateLanguage' => true,
            'Recipients' => [
                ['Email' => "contact@smartmoov.solutions"],
            ],
            'Vars' => json_decode($contact, true)
        ];
        $response =  $mj->post(Resources::$Email, ['body' => $body]);
        $response->success() && var_dump($response->getData());
    }

    /**
     *  Envoi d'un mail au client pour comfirmer l'envoie de ses informations
     **/
    public function sendMailClient($contact){
        $mj = Mailjet::getClient();
        $body = [
            'FromEmail' => "contact@assurance-conduite.fr",
            'FromName' => "Assurance Conduite",
            'Subject' => "Votre demande à bien été transmise",
            'MJ-TemplateLanguage' => true,
            "MJ-TemplateID" => '1835726',
            'Recipients' => [
                [
                    'Email' => "$contact->email",
                    'Name' => $contact->name
                ]
            ]
        ];
        $response =  $mj->post(Resources::$Email, ['body' => $body]);
        $response->success() && var_dump($response->getData());
    }
}
