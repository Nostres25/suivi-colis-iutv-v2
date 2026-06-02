install:
	composer install --prefer-dist --no-progress --no-interaction --optimize-autoloader

prod-suivi-colis-iutv:
	@echo "Pulling Sae Suivis Colis..."
	sudo docker pull ghcr.io/at9ph/suivi-colis-iutv:latest
	@echo "Prod Sae Suivis Colis pulled successfully."
	sudo docker images
	cd sae && sudo docker stack deploy --with-registry-auth -c colis.yaml ProdSuiviColisIutv
	sleep 15
	sudo docker exec $$(sudo docker ps -qf "name=sae-jupiter") a2dismod cas && sudo docker exec $$(sudo docker ps -qf "name=sae-jupiter") apache2ctl graceful
	sudo docker images
	sudo docker ps