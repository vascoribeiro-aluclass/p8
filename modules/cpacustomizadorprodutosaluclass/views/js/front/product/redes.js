// Ficheiro JS criado automaticamente


function getStart_Override() {

    let defaultColor = $(".img-value.is_visual.treed.select-value").data(
        "color",
    );

    if (defaultColor) {
        toggleMaterial(defaultColor);
    }

    let defaulidfield = $('.dimension_text_height').first().data("field");

    if (defaulidfield) {
        GetSize(defaulidfield);
    }
    
}

function toggleMaterial_Override(color) {
    CPA3Dmanager.setColor(color);
    CPA3Dmanager.objects.forEach((obj) => {
        CPA3Dmanager.setupObject(obj, 'plast',  { metalness: 0, roughness: 0.55});
    });
    
}

function toggleSize_Override(
    width, widthMin, widthMax,
    height, heightMin, heightMax,
    depth, depthMin, depthMax
) {

    width = width > 10 ? 10 : width;
    oldsize = CPA3Dmanager.extraModal;

    if(oldsize == width )
        return;

    for (i = 1; i < oldsize + 1; i++) {
        CPA3Dmanager.removeModel("sec-" + i)
    }
    CPA3Dmanager.extraModal = width;
    const spacing = 253;
    for (i = 1; i < width + 1; i++) {
        CPA3Dmanager.addModel(
            modulePath +
            "views/js/front/3d/product/48485-1.fbx",
            {
                positionx: spacing * i,
                positiony: 0,
                positionz: 0,
                id: "sec-" + i,
                scaleX: 1,
                scaleY: 1,
                scaleZ: 1,
                meshName: "plast",
                materialOptions: {
                    metalness: 0,
                    roughness: 0.55
                }
            }
        );
    }

    const centerX = (spacing * (width - 1)) / 2;
    const center = new THREE.Vector3(
        centerX,
        0,
        0
    );

    CPA3Dviewer.updateBackground(
        center       // centro do conjunto
    );
}