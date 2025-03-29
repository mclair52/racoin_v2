<?php

namespace controller;

use model\Annonce;
use model\Annonceur;

class addItem
{

    function addItemView($twig, $menu, $chemin, $cat, $dpt)
    {
        $template = $twig->load("add.html.twig");
        echo $template->render(array(
                "breadcrumb"   => $menu,
                "chemin"       => $chemin,
                "categories"   => $cat,
                "departements" => $dpt
            )
        );

    }

    private function isEmail($email)
    {
        return (preg_match("/^[-_.[:alnum:]]+@((([[:alnum:]]|[[:alnum:]][[:alnum:]-]*[[:alnum:]])\.)+(ad|ae|aero|af|ag|ai|al|am|an|ao|aq|ar|arpa|as|at|au|aw|az|ba|bb|bd|be|bf|bg|bh|bi|biz|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|com|coop|cr|cs|cu|cv|cx|cy|cz|de|dj|dk|dm|do|dz|ec|edu|ee|eg|eh|er|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gb|gd|ge|gf|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|id|ie|il|in|info|int|io|iq|ir|is|it|jm|jo|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|mg|mh|mil|mk|ml|mm|mn|mo|mp|mq|mr|ms|mt|mu|museum|mv|mw|mx|my|mz|na|name|nc|ne|net|nf|ng|ni|nl|no|np|nr|nt|nu|nz|om|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pro|ps|pt|pw|py|qa|re|ro|ru|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sy|sz|tc|td|tf|tg|th|tj|tk|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|um|us|uy|uz|va|vc|ve|vg|vi|vn|vu|wf|ws|ye|yt|yu|za|zm|zw)$|(([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5])\.){3}([0-9][0-9]?|[0-1][0-9][0-9]|[2][0-4][0-9]|[2][5][0-5]))$/i", $email));
    }

    function addNewItem($twig, $menu, $chemin, $allPostVars)
    {
        date_default_timezone_set('Europe/Paris');

        // Liste des champs à récupérer
        $fields = [
            'nom', 'email', 'phone', 'ville', 'departement', 
            'categorie', 'title', 'description', 'price', 
            'psw', 'confirm-psw'
        ];

        // Récupération et nettoyage des champs
        $data = [];
        foreach ($fields as $field) {
            $data[$field] = isset($_POST[$field]) ? trim($_POST[$field]) : '';
        }

        // Tableau d'erreurs personnalisées
        $errorMessages = [
            'nameAdvertiser'        => 'Veuillez entrer votre nom',
            'emailAdvertiser'       => 'Veuillez entrer une adresse mail correcte',
            'phoneAdvertiser'       => 'Veuillez entrer votre numéro de téléphone',
            'villeAdvertiser'       => 'Veuillez entrer votre ville',
            'departmentAdvertiser'  => 'Veuillez choisir un département',
            'categorieAdvertiser'   => 'Veuillez choisir une catégorie',
            'titleAdvertiser'       => 'Veuillez entrer un titre',
            'descriptionAdvertiser' => 'Veuillez entrer une description',
            'priceAdvertiser'       => 'Veuillez entrer un prix',
            'passwordAdvertiser'    => 'Les mots de passes ne sont pas identiques'
        ];

        $errors = [];

        // Validation des champs
        if (empty($data['nom'])) {
            $errors['nameAdvertiser'] = $errorMessages['nameAdvertiser'];
        }
        if (!$this->isEmail($data['email'])) {
            $errors['emailAdvertiser'] = $errorMessages['emailAdvertiser'];
        }
        if (empty($data['phone']) || !is_numeric($data['phone'])) {
            $errors['phoneAdvertiser'] = $errorMessages['phoneAdvertiser'];
        }
        if (empty($data['ville'])) {
            $errors['villeAdvertiser'] = $errorMessages['villeAdvertiser'];
        }
        if (!is_numeric($data['departement'])) {
            $errors['departmentAdvertiser'] = $errorMessages['departmentAdvertiser'];
        }
        if (!is_numeric($data['categorie'])) {
            $errors['categorieAdvertiser'] = $errorMessages['categorieAdvertiser'];
        }
        if (empty($data['title'])) {
            $errors['titleAdvertiser'] = $errorMessages['titleAdvertiser'];
        }
        if (empty($data['description'])) {
            $errors['descriptionAdvertiser'] = $errorMessages['descriptionAdvertiser'];
        }
        if (empty($data['price']) || !is_numeric($data['price'])) {
            $errors['priceAdvertiser'] = $errorMessages['priceAdvertiser'];
        }
        if (empty($data['psw']) || empty($data['confirm-psw']) || $data['psw'] != $data['confirm-psw']) {
            $errors['passwordAdvertiser'] = $errorMessages['passwordAdvertiser'];
        }

        // S'il y a des erreurs, on redirige vers la page d'erreur
        if (!empty($errors)) {
            $template = $twig->load("add-error.html.twig");
            echo $template->render([
                "breadcrumb" => $menu,
                "chemin"     => $chemin,
                "errors"     => $errors
            ]);
        } else {
            // Création des objets Annonce et Annonceur
            $annonce   = new Annonce();
            $annonceur = new Annonceur();

            // Remplissage des données
            foreach (['email', 'nom', 'phone'] as $field) {
                $annonceur->$field = htmlentities($data[$field]);
            }

            foreach ([
                'ville', 'departement' => 'id_departement', 
                'price' => 'prix', 'psw' => 'mdp', 
                'title' => 'titre', 'description', 
                'categorie' => 'id_categorie'
            ] as $key => $field) {
                $fieldName = is_numeric($key) ? $field : $key;
                $annonce->$field = $fieldName === 'mdp' 
                    ? password_hash($data[$fieldName], PASSWORD_DEFAULT) 
                    : htmlentities($data[$fieldName]);
            }

            $annonce->date = date('Y-m-d');

            // Sauvegarde dans la base de données
            $annonceur->save();
            $annonceur->annonce()->save($annonce);

            // Redirection vers la page de confirmation
            $template = $twig->load("add-confirm.html.twig");
            echo $template->render([
                "breadcrumb" => $menu,
                "chemin"     => $chemin
            ]);
        }
    }
    }
