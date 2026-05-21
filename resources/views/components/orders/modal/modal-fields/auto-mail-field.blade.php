<div class="d-flex justify-content-start" title="Cocher pour envoyer un mail automatique aux acteurs concernés lorsque le bon de commande sera déposé">
    <input class="form-check-input me-2" type="checkbox" name="sendMail"
           id="checkboxMail-{{$orderId}}" form="addPurchaseOrder-{{$orderId}}" checked>
    <label class="form-check-label" for="checkboxMail-{{$orderId}}">
        Envoyer un mail automatique pour avertir de la modification
    </label>
</div>
<div id="mailOptionsDiv-{{$orderId}}">
    <label class="form-label fs-6"><a class="" data-bs-toggle="collapse"
                                      href="#mailOptions-{{$orderId}}" role="button"
                                      aria-expanded="false"
                                      aria-controls="mailOptions-{{$orderId}}">Modifier l'email automatique
            ></a></label>
    <div class="collapse" id="mailOptions-{{$orderId}}">
        {{-- TODO mettre cela à jour à la fin du projet. Il est possible avec le travail sur les notifications qu'il y ait aussi les personnes ayant activé commenté et ayant activé les notificaitons pour la commande--}}
        <dl class="fw-light">
            Modifiez le contenu par défaut pour l'adapter au besoin.<br/>
            Les receveurs du mail automatique seront l'auteur de la commande et l'acteur chargé de la prochaine étape.
        </dl>
        <div class="mb-3">

            <label for="mailContent-{{$orderId}}" class="col-form-label fs-6">Contenu :</label>
            <textarea
                class="form-control"
                style="height: 200px"
                name="mailContent"
                id="mailContent-{{$orderId}}"
            >{{$defaultMailContent}}</textarea>
        </div>
    </div>
</div>
