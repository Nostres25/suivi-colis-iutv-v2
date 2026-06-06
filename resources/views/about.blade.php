@extends('base')

@section('header')
    <div class="container d-block">
        <h1 class="h1">À propos du projet</h1>
        <p class="mb-0 opacity-75">
            Solution de suivi de colis développée au département Informatique de l'IUT à Villetaneuse
        </p>
    </div>
@endsection

@section('content')

    <section>
        <div class="table-header">
            <h2>Objectif du Projet</h2>
            <p>Pourquoi ce projet existe</p>
        </div>

        <div class="p-4">
            <p class="mb-4 fs-5">
                Ce projet a été initié dans le cadre d'un projet évalué d'étudiants en deuxième année de BUT Informatique (SAÉ 3.01), dont l’objectif était de concevoir une première version d’un site internet de suivi des colis pour l’IUT de Villetaneuse. Cette application web doit fluidifier le traitement des commandes et des colis des départements de l'IUT ainsi que de réduire les pertes.<br/>
                Le projet a ensuite été repris pour le second projet évalué de l'année (SAÉ 4.01), qui consiste à reprendre le projet d'une autre équipe afin de le finaliser ou/et de l'améliorer.<br/>
            </p>
            <h5 class="mb-3 fw-bold">Objectifs principaux</h5>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="bg-primary text-white p-4 rounded shadow h-100">
                        <h5 class="fw-bold mb-3">Suivi en temps réel</h5>
                        <p class="mb-0 opacity-90">
                            Suivre chaque colis depuis la commande jusqu'à la livraison et le paiement pour éviter les pertes et les ralentissements.
                        </p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="bg-primary text-white p-4 rounded shadow h-100">
                        <h5 class="fw-bold mb-3">Gestion centralisée</h5>
                        <p class="mb-0 opacity-90">
                            Regrouper toutes les informations sur les commandes au même endroit pour faciliter l'accès aux informations sur les commandes.
                        </p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="bg-primary text-white p-4 rounded shadow h-100">
                        <h5 class="fw-bold mb-3">Interface simple</h5>
                        <p class="mb-0 opacity-90">
                            Proposer un outil accessible et intuitif quel que soit le niveau technique de l'utilisateur.
                        </p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="bg-primary text-white p-4 rounded shadow h-100">
                        <h5 class="fw-bold mb-3">Solution pratique</h5>
                        <p class="mb-0 opacity-90">
                            Créer un outil facile et rapide à utiliser, qui répond correctement aux besoins de traitement et de suivi des commandes.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section>
        <div class="table-header">
            <h2>Les équipes du projet</h2>
            <p>Présentation des équipes ayant participé au développement</p>
        </div>

        <div class="p-4">

            {{-- Équipe A --}}
            <div class="alert alert-light border mb-4">
                <h5 class="mb-3 fw-bold">Équipe A (septembre 2025 - janvier 2026)</h5>
                <p class="mb-3">
                    Cette équipe a conçu et développé la première version de l'application dans le cadre de la SAE 3.01.
                </p>

                <h6 class="mb-3 fw-bold">Membres de l'équipe A</h6>
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="card border shadow-sm">
                            <div class="card-body text-center py-3">
                                <h6 class="mb-0 fw-semibold">Soan MOREAU</h6>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border shadow-sm">
                            <div class="card-body text-center py-3">
                                <h6 class="mb-0 fw-semibold">Weame EL MOUTTAQUI</h6>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border shadow-sm">
                            <div class="card-body text-center py-3">
                                <h6 class="mb-0 fw-semibold">Yasmine AIT SALAH</h6>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border shadow-sm">
                            <div class="card-body text-center py-3">
                                <h6 class="mb-0 fw-semibold">Myriam ABDELLAOUI</h6>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border shadow-sm">
                            <div class="card-body text-center py-3">
                                <h6 class="mb-0 fw-semibold">Dimitar DIMITROV</h6>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border shadow-sm">
                            <div class="card-body text-center py-3">
                                <h6 class="mb-0 fw-semibold">Megane MAZEKEM</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Équipe B --}}
            <div class="alert alert-light border mb-4">
                <h5 class="mb-3 fw-bold">Équipe B (avril 2026 - juin 2026)</h5>
                <p class="mb-3">
                    L’équipe B a repris ce travail dans le cadre de la SAÉ 4.01 afin de corriger les
                    problèmes identifiés, d’ajouter de nouvelles fonctionnalités et de finaliser
                    l’application.
                </p>

                <h6 class="mb-3 fw-bold">Membres de l'équipe B</h6>
                <div class="row g-3">
                    {{-- Remplacez ces noms par les membres de votre équipe --}}
                    <div class="col-md-4">
                        <div class="card border shadow-sm">
                            <div class="card-body text-center py-3">
                                <h6 class="mb-0 fw-semibold">Soan MOREAU</h6>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border shadow-sm">
                            <div class="card-body text-center py-3">
                                <h6 class="mb-0 fw-semibold"> Sanjai RAMASAMY</h6>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border shadow-sm">
                            <div class="card-body text-center py-3">
                                <h6 class="mb-0 fw-semibold"> Gopi SURESH</h6>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 offset-md-2">
                        <div class="card border shadow-sm">
                            <div class="card-body text-center py-3">
                                <h6 class="mb-0 fw-semibold"> Sarah HELLAL</h6>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card border shadow-sm">
                            <div class="card-body text-center py-3">
                                <h6 class="mb-0 fw-semibold"> Lissam LOUTFI</h6>
                            </div>
                        </div>
                    </div>
                    {{-- Organisation --}}
                    <h6 class="mb-3 fw-bold">Organisation de l'équipe B</h6>
                    <p class="mb-0">
                        Tous les membres de l'équipe participent aux différentes tâches du projet :<br/>
                        analyse, organisation, conception de la base de données, développement, tests et rédaction de la documentation.
                    </p>
                </div>
            </div>
            <div class="alert alert-light border">
                <h5 class="mb-3 fw-bold">Maintenance et gestion du site</h5>
                <p class="mb-0">
                    Le responsable du site lors de son utilisation en situation réelle est M. <strong>Franck BUTELLE</strong>, responsable CRIT à l'IUTV et enseignant chercheur. Qui est aussi le demandeur du projet. Il s'agit notamment de la personne à contacter si vous rencontrez des difficultés avec ce site.
                </p>
            </div>
        </div>
    </section>

@endsection
