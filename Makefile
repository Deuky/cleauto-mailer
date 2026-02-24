DOCKER_TAG?="latest"
DOCKER_ARTIFACT?=$(shell docker create ghcr.io/deuky/cleauto-form/artifact:$(DOCKER_TAG) bash)
.PHONY: public/*

configure: public/* templates/app

.artifact:
	docker cp $(DOCKER_ARTIFACT):/artifact .artifact; \
	docker rm $(DOCKER_ARTIFACT)

public/*: .artifact
	cp -rv .artifact/* ./public

templates/app: .artifact 
	mkdir -vp $@; \
	cp .artifact/index.html $@/index.html.twig

clear:
	rm -rf .artifact templates/app public;
	git checkout public