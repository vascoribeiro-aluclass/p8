var CPA3Dviewer = null;
var CPA3Dmanager = null;

const backgrounds = [
    "BGCompleto_360.jpg",
    "Piso_360.jpg",
    "img360.jpg"
];

let backgroundIndex = 0;


function show3D() {

    const productCover = $(".product-cover");
    const exists = $("#3dshow").length > 0;

    if (!exists) {
        const container = $("<div>", {
            id: "3dshow",
            class: "3dshow",
            css: {
                width: "100%",
                height: "100%",
                position: "absolute",
                inset: 0,
                zIndex: 998
            }
        });

        productCover.prepend(container);

        /*BOTÃO BACKGROUND*/
        const btnBackground = $("<button>", {
            class: "btn btn-primary",
            text: "Mudar ambiente",
            css: {
                position: "absolute",
                left: "10px",
                top: "10px",
                zIndex: 999
            }
        });

        container.append(btnBackground);


        /*VIEWER*/
        CPA3Dviewer = new Viewer3D("3dshow");
        CPA3Dviewer.setBackground(
            modulePath +
            "views/js/front/3d/" +
            backgrounds[backgroundIndex]
        );

        /*MODEL MANAGER*/
        CPA3Dmanager = new ModelManager(
            CPA3Dviewer,
            new THREE.FBXLoader()
        );

        /*COR INICIAL*/
        let defaultColor =
            $('.img-value.is_visual.treed.select-value')
                .data('color');

        if (!defaultColor) {
            defaultColor = '#383E42';
        }

        /*LOAD MODEL*/
        CPA3Dmanager
            .setColor(defaultColor)
            .setMetalness(0.7)
            .setRoughness(0.35)
            .setPosition(0, -100, -12)
            .setScale(0.1, 0.1, 0.1);

        CPA3Dmanager.addModel(
            modulePath +
            "views/js/front/3d/product/" +
            name3dshow
        );

        /*BOTÃO TROCAR FUNDO*/
        btnBackground.on("click", function () {
            backgroundIndex++;
            if (backgroundIndex >= backgrounds.length) {
                backgroundIndex = 0;
            }
            CPA3Dviewer.setBackground(
                modulePath +
                "views/js/front/3d/" +
                backgrounds[backgroundIndex]
            );
        });


        /*TROCAR COR*/
        $('.img-value.is_visual.treed')
            .off('click')
            .on('click', function () {
                let color = $(this).data('color');
                toggleMaterial(color);
            });


        /*TROCAR TAMANHO*/
        $('.cpa_dimension_text')
            .off('change')
            .on('change', function () {

                const idfield = $(this).data('field');

                const widthMin = parseInt($('.dimension_text_width[data-field="' + idfield + '"]').attr('min'));
                const widthMax = parseInt($('.dimension_text_width[data-field="' + idfield + '"]').attr('max'));

                const heightMin = parseInt($('.dimension_text_height[data-field="' + idfield + '"]').attr('min'));
                const heightMax = parseInt($('.dimension_text_height[data-field="' + idfield + '"]').attr('max'));

                const depthMin = parseInt($('.dimension_text_depth[data-field="' + idfield + '"]').attr('min'));
                const depthMax = parseInt($('.dimension_text_depth[data-field="' + idfield + '"]').attr('max'));

                const width = parseInt($('.dimension_text_width[data-field="' + idfield + '"]').val()) || widthMin;
                const height = parseInt($('.dimension_text_height[data-field="' + idfield + '"]').val()) || heightMin;
                const depth = parseInt($('.dimension_text_depth[data-field="' + idfield + '"]').val()) || depthMin;

                toggleSize(width, widthMin, widthMax, height, heightMin, heightMax, depth, depthMin, depthMax);

            });

    } else {

        /*DESTROY */
        if (CPA3Dmanager) {
            CPA3Dmanager.clear();
            CPA3Dmanager = null;
        }

        if (CPA3Dviewer) {

            CPA3Dviewer.controls.dispose();

            CPA3Dviewer.renderer.dispose();

            CPA3Dviewer.renderer.domElement.remove();

            $("#3dshow").remove();

            CPA3Dviewer = null;
        }

        $('.img-value.is_visual.treed').off('click');
        $('.cpa_dimension_text').off('change');
    }
}

function toggleMaterial(color) {
    if (typeof (toggleMaterial_Override) == 'function') {
        return toggleMaterial_Override(color);
    }

    if (!color) {
        color = '#383E42';
    }
    CPA3Dmanager.setColor(color);
}

function toggleSize(width, widthMin, widthMax, height, heightMin, heightMax, depth, depthMin, depthMax) {
    if (typeof (toggleSize_Override) == 'function') {
        return toggleSize_Override(width, widthMin, widthMax, height, heightMin, heightMax, depth, depthMin, depthMax);
    }

    /*SCALE MAP*/
    const scaleX = mapRange(width, widthMin, widthMax, 0.1, 0.15);
    const scaleY = mapRange(height, heightMin, heightMax, 0.1, 0.15);
    const scaleZ = mapRange(depth, depthMin, depthMax, 0.1, 0.15);

    CPA3Dmanager.setScale(scaleX, scaleY, scaleZ);
}



function mapRange(value, inMin, inMax, outMin, outMax) {

    if (inMax === inMin) {
        return outMin;
    }

    let t = (value - inMin) / (inMax - inMin);
    t = Math.max(0, Math.min(1, t));

    return outMin + (t * (outMax - outMin));
}