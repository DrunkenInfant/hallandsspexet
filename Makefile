PLUGIN_NAMES=food users tab
BUILD_DIR=build

.PHONY: all plugins

all: plugins

plugins: $(patsubst %,${BUILD_DIR}/hallandsspexet-%.zip,${PLUGIN_NAMES})

${BUILD_DIR}/%.zip: %/*.php %/*.css | ${BUILD_DIR}
	    zip -r $@ $*

${BUILD_DIR}:
	mkdir -p ${BUILD_DIR}
