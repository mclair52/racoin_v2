<?php

namespace controller;

use model\Categorie;
use model\Annonce;
use model\Photo;
use model\Annonceur;

class getCategorie {

    protected array $categories = [];
    protected array $annonce = [];

    public function getCategories(): array {
        return Categorie::orderBy('nom_categorie')->get()->toArray();
    }

    public function getCategorieContent(string $chemin, int $n): void {
        $tmp = Annonce::with("Annonceur")
            ->orderBy('id_annonce', 'desc')
            ->where('id_categorie', $n)
            ->get();

        $annonce = [];
        foreach ($tmp as $t) {
            $t->nb_photo = Photo::where("id_annonce", $t->id_annonce)->count();
            $t->url_photo = $t->nb_photo > 0
                ? Photo::select("url_photo")
                    ->where("id_annonce", $t->id_annonce)
                    ->first()?->url_photo
                : $chemin . '/img/noimg.png';

            $t->nom_annonceur = Annonceur::select("nom_annonceur")
                ->where("id_annonceur", $t->id_annonceur)
                ->first()?->nom_annonceur;

            $annonce[] = $t;
        }
        $this->annonce = $annonce;
    }

    public function displayCategorie($twig, array $menu, string $chemin, array $cat, int $n): void {
        $template = $twig->load("index.html.twig");
        $menu = [
            [
                'href' => $chemin,
                'text' => 'Acceuil'
            ],
            [
                'href' => $chemin . "/cat/" . $n,
                'text' => Categorie::find($n)?->nom_categorie
            ]
        ];

        $this->getCategorieContent($chemin, $n);
        echo $template->render([
            "breadcrumb" => $menu,
            "chemin" => $chemin,
            "categories" => $cat,
            "annonces" => $this->annonce
        ]);
    }
}
