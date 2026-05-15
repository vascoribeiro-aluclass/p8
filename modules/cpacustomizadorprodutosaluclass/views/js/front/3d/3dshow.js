var CPA3Dviewer = null;
var CPA3Dmanager = null;

/*
=========================
BACKGROUNDS
=========================
*/

var CPA3Dbackgrounds = ["BGCompleto_360.jpg", "Piso_360.jpg", "img360.jpg"];

var CPA3DbackgroundIndex = 0;

/*
=========================
SHOW / HIDE 3D
=========================
*/

async function show3D() {
    const productCover = $("#content > .images-container > .product-cover");
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
                zIndex: 998,
            },
        });

        productCover.prepend(container);

        const btnBackground = $("<button>", {
            class: "btn btn-primary",
            id: "3dshowambient",
            text: "Mudar ambiente",
            css: {
                position: "absolute",
                left: "10px",
                top: "10px",
                zIndex: 999,
            },
        });

        productCover.append(btnBackground);

        CPA3Dviewer = new Viewer3D("3dshow");
        CPA3Dviewer.setBackground(
            modulePath + "views/js/front/3d/" + CPA3Dbackgrounds[CPA3DbackgroundIndex],
        );

        CPA3Dmanager = new ModelManager(
            CPA3Dviewer,
            new THREE.FBXLoader(),
        );



        CPA3Dmanager.setColor('#383E42')
            .setMetalness(0.15)
            .setRoughness(0.55)
            .setPosition(0, 0, 0)
            .setScale(1, 1, 1);

        await CPA3Dmanager.addModel(
            modulePath + "views/js/front/3d/product/" + name3dshow,
            {
                id: "main",
                positionx: 0,
                positiony: 0,
                positionz: 0,

                scaleX: 1,
                scaleY: 1,
                scaleZ: 1
            }
        );

        btnBackground.on("click", function () {
            CPA3DbackgroundIndex++;

            if (CPA3DbackgroundIndex >= CPA3Dbackgrounds.length) {
                CPA3DbackgroundIndex = 0;
            }

            CPA3Dviewer.setBackground(
                modulePath + "views/js/front/3d/" + CPA3Dbackgrounds[CPA3DbackgroundIndex],
            );
        });

        getStart();
        startEvent();

    } else {

        if (CPA3Dmanager) {
            CPA3Dmanager.clear();
            CPA3Dmanager = null;
        }

        if (CPA3Dviewer) {
            CPA3Dviewer.destroy();
            $("#3dshow").remove();
            $("#3dshowambient").remove();
            CPA3Dviewer = null;
        }

        $(".img-value.is_visual.treed").off("click");
        $(".cpa_dimension_text").off("change");
    }
}

function startEvent() {
    if (typeof startEvent_Override == "function") {
        return startEvent_Override();
    }
    $(".img-value.is_visual.treed")
        .off("click")
        .on(
            "click",
            function () {
                let color = $(this).data("color");
                toggleMaterial(color);
            },
        );


    $(".cpa_dimension_text")
        .off("change")
        .on("change",
            function () {
                const idfield = $(this).data("field");
                GetSize(idfield);
            },
        );
}

function getStart() {
    if (typeof getStart_Override == "function") {
        return getStart_Override();
    }

    let defaultColor = $(".img-value.is_visual.treed.select-value").data(
        "color",
    );

    if (defaultColor) {
        toggleMaterial(defaultColor);
    }

    let defaulidfield = $('.dimension_text_height:visible').first().data("field");

    if (defaulidfield) {
        GetSize(defaulidfield);
    }
}

function GetSize(idfield) {
    const widthMin = parseInt($('.dimension_text_width[data-field="' + idfield + '"]').attr("min"));
    const widthMax = parseInt($('.dimension_text_width[data-field="' + idfield + '"]').attr("max"));

    const heightMin = parseInt($('.dimension_text_height[data-field="' + idfield + '"]').attr("min"));
    const heightMax = parseInt($('.dimension_text_height[data-field="' + idfield + '"]').attr("max"));

    const depthMin = parseInt($('.dimension_text_depth[data-field="' + idfield + '"]').attr("min"));
    const depthMax = parseInt($('.dimension_text_depth[data-field="' + idfield + '"]').attr("max"));

    const width = parseInt($('.dimension_text_width[data-field="' + idfield + '"]').val()) || widthMin;
    const height = parseInt($('.dimension_text_height[data-field="' + idfield + '"]').val()) || heightMin;
    const depth = parseInt($('.dimension_text_depth[data-field="' + idfield + '"]').val()) || depthMin;

    toggleSize(width, widthMin, widthMax, height, heightMin, heightMax, depth, depthMin, depthMax);
}


function toggleMaterial(color) {

    if (typeof toggleMaterial_Override == "function") {
        return toggleMaterial_Override(color);
    }

    if (!color) {
        color = "#383E42";
    }

    CPA3Dmanager.setColor(color);
}

function toggleSize(width, widthMin, widthMax, height, heightMin, heightMax, depth, depthMin, depthMax) {

    if (typeof toggleSize_Override == "function") {
        return toggleSize_Override(width, widthMin, widthMax, height, heightMin, heightMax, depth, depthMin, depthMax);
    }

    const scaleX = mapRange(width, widthMin, widthMax, 1, 1.15);
    const scaleY = mapRange(height, heightMin, heightMax, 1, 1.15);
    const scaleZ = mapRange(depth, depthMin, depthMax, 1, 1.15);

    CPA3Dmanager.setScale(scaleX, scaleY, scaleZ);
}



function mapRange(value, inMin, inMax, outMin, outMax) {
    if (inMax === inMin) {
        return outMin;
    }

    let t = (value - inMin) / (inMax - inMin);
    t = Math.max(0, Math.min(1, t));

    return outMin + t * (outMax - outMin);
}
