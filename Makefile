DOCKER_ARTIFACT?=$(shell docker create ghcr.io/deuky/cleauto-form/artifact:latest bash)

configure: public/assets templates/app

.artifact:
	docker cp $(DOCKER_ARTIFACT):/artifact .artifact; \
	docker rm $(DOCKER_ARTIFACT)

public/assets: .artifact
	cp -rv .artifact/assets ./public

templates/app: .artifact 
	mkdir -vp $@; \
	cp .artifact/index.html $@/index.html.twig