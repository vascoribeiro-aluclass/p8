if (!Detector.webgl) Detector.addGetWebGLMessage();



var CPA3Dcontrols;
var CPA3Dcamera, CPA3Drenderer, CPA3Dcontainer;
var CPA3DfbxObject;
var CPA3DbackgroundMesh;
var CPA3DdynamicRadius;
var CPA3DindexFundo = 0;
var CPA3Dimagens = ["BGCompleto_360.jpg", "Piso_360.jpg", "img360.jpg"];
var CPA3Dloader = new THREE.FBXLoader();
var CPA3Dscene = new THREE.Scene();
var CPA3Dcolor, CPA3DmaxDim;

function changeBackground() {
    CPA3DindexFundo = (CPA3DindexFundo + 1) % CPA3Dimagens.length;
    setupBackground(CPA3Dimagens[CPA3DindexFundo]);
}

function init(color) {
    // Seleciona a div #3dshow existente
    CPA3Dcontainer = document.getElementById("3dshow");

    if (!CPA3Dcontainer) {
        console.error("Div #3dshow não encontrada!");
        return;
    }

    // Cria a cena
    CPA3Dscene.background = new THREE.Color(0x89d0ff);

    // Câmera
    CPA3Dcamera = new THREE.PerspectiveCamera(
        75,
        CPA3Dcontainer.clientWidth / CPA3Dcontainer.clientHeight, // tamanho do container
        0.1,
        5000
    );

    // Luzes
    ambientLight();

    // Controles
    CPA3Dcontrols = new THREE.OrbitControls(CPA3Dcamera, CPA3Dcontainer);
    CPA3Dcontrols.addEventListener("change", render);
    CPA3Dcontrols.enableDamping = true;
    CPA3Dcontrols.dampingFactor = 0.25;
    CPA3Dcontrols.enableZoom = true;
    CPA3Dcontrols.minPolarAngle = 0;
    CPA3Dcontrols.maxPolarAngle = Math.PI * 0.55;

    // Loader do FBX

    CPA3Dloader.load(modulePath + "views/js/front/3d/product/" + name3dshow, function (object) {
        CPA3DfbxObject = object;
        CPA3DfbxObject.scale.set(0.1, 0.1, 0.1);
        CPA3DfbxObject.position.y -= 100;
        CPA3DfbxObject.position.z -= 12;

        if (color) {
            if (typeof color === "string" && !color.startsWith('#'))
                color = '#' + color;
            toggleMaterial(color);
        }

        // Centraliza a câmera
        const boundingBox = new THREE.Box3().setFromObject(CPA3DfbxObject);
        const size = new THREE.Vector3();
        boundingBox.getSize(size);

        CPA3DmaxDim = Math.max(size.x, size.y, size.z);
        CPA3DdynamicRadius = CPA3DmaxDim * 10;

        setupBackground("BGCompleto_360.jpg");

        const offset = 1.2;
        const fov = CPA3Dcamera.fov * (Math.PI / 180);
        const fovh = 2 * Math.atan(Math.tan(fov / 2) * CPA3Dcamera.aspect);
        let dx = size.z / 2 + Math.abs(size.x / 2 / Math.tan(fovh / 2));
        let dy = size.z / 2 + Math.abs(size.y / 2 / Math.tan(fov / 2));
        let cameraZ = Math.max(dx, dy);
        if (offset) cameraZ *= offset;

        CPA3Dcamera.position.set(0, 0, cameraZ);

        const minZ = boundingBox.min.z;
        const cameraToFarEdge = minZ < 0 ? -minZ + cameraZ : cameraZ - minZ;
        CPA3Dcontrols.maxDistance = cameraToFarEdge * 2;
        CPA3Dcontrols.target.set(0, 0, CPA3DfbxObject.position.z);

        CPA3Dscene.add(CPA3DfbxObject);
    },
        function (xhr) {
            console.log((xhr.loaded / xhr.total * 100) + "% carregado");
        }, function (error) {
            console.error("Erro ao carregar FBX:", error);
        });



    // Renderer
    CPA3Drenderer = new THREE.WebGLRenderer({ antialias: true });
    CPA3Drenderer.setPixelRatio(window.devicePixelRatio);
    CPA3Drenderer.setSize(CPA3Dcontainer.clientWidth, CPA3Dcontainer.clientHeight);
    CPA3Dcontainer.appendChild(CPA3Drenderer.domElement);

    // Redimensionamento
    window.addEventListener("resize", onWindowResize, false);
    // toggleMaterial(color);
}

function ambientLight() {

    if (typeof (ambientLight_Override) == 'function') {
        return ambientLight_Override(color);
    }
    CPA3Dscene.add(new THREE.AmbientLight(0xffffff, 1.2));

    const hemi = new THREE.HemisphereLight(0xffffff, 0xdddddd, 1.5);
    CPA3Dscene.add(hemi);

    // luz de “forma” (essencial para relevo)
    const dir = new THREE.DirectionalLight(0xffffff, 1.0);
    dir.position.set(5, 10, 5);
    CPA3Dscene.add(dir);
}

function onWindowResize() {
    if (!CPA3Dcontainer) return;
    CPA3Dcamera.aspect = CPA3Dcontainer.clientWidth / CPA3Dcontainer.clientHeight;
    CPA3Dcamera.updateProjectionMatrix();
    CPA3Drenderer.setSize(CPA3Dcontainer.clientWidth, CPA3Dcontainer.clientHeight);
    render();

}

function animate() {
    requestAnimationFrame(animate);
    CPA3Dcontrols.update();
    render();
}

function render() {
    CPA3Drenderer.render(CPA3Dscene, CPA3Dcamera);
}

function setupBackground(imageName, xcamera = 0) {

    if (!CPA3DbackgroundMesh) {

        const geometry = new THREE.SphereGeometry(2000, 30, 20);
        geometry.scale(-1, 1, 1);

        const texture = new THREE.TextureLoader().load(
            modulePath + "views/js/front/3d/" + imageName
        );

        const material = new THREE.MeshBasicMaterial({ map: texture });

        CPA3DbackgroundMesh = new THREE.Mesh(geometry, material);
        CPA3DbackgroundMesh.position.y = 5;

        CPA3Dscene.add(CPA3DbackgroundMesh);
    }

    // sempre atualiza isto
    updateBackground(imageName, xcamera);
}

function updateBackground(imageName, xcamera = 0) {

    const texture = new THREE.TextureLoader().load(
        modulePath + "views/js/front/3d/" + imageName
    );

    CPA3DbackgroundMesh.material.map = texture;
    CPA3DbackgroundMesh.material.needsUpdate = true;

    if (xcamera > 0) {
        CPA3DbackgroundMesh.position.x = xcamera;
    }


}

// function toggleMaterial(color) {
//     if (!CPA3DfbxObject) return;

//     let paintParts = [];

//     CPA3DfbxObject.traverse((child) => {
//         if (child.isMesh && child.name.startsWith("paint")) {
//             paintParts.push(child);
//         }
//     });

//     // depois:

//     paintParts.forEach(part => {
//                 part.material = new THREE.MeshStandardMaterial({
//             color: color,
//             metalness: 0.5,
//             roughness: 0.5
//         });
//         part.material.needsUpdate = true;
//     });

// }

function toggleMaterial(color, fbxObjectnew = false) {
    if (!CPA3DfbxObject) return;
    let fbxObjectchange = fbxObjectnew ? fbxObjectnew : CPA3DfbxObject;

    CPA3Dcolor = color;

    if (typeof (toggleMaterial_Override) == 'function') {
        return toggleMaterial_Override(color, fbxObjectnew);
    }

    fbxObjectchange.traverse(function (child) {
        if (child.isMesh) {
            child.material = new THREE.MeshStandardMaterial({
                color: color,
                metalness: 0.7,
                roughness: 0.35
            });
            child.material.needsUpdate = true;
        }
    });
}


function mapRange(value, inMin, inMax, outMin, outMax) {
    if (inMax === inMin) return outMin;

    let t = (value - inMin) / (inMax - inMin);
    t = Math.max(0, Math.min(1, t));

    return outMin + t * (outMax - outMin);
}

function safe(val, fallback = 1) {
    return (val === 0 || val === null || val === undefined) ? fallback : val;
}

function toggleSize(width, widthMin, widthMax, height, heightMin, heightMax, depth, depthMin, depthMax, fbxObjectnew = false) {
    if (!CPA3DfbxObject) return;
    let fbxObjectchange = fbxObjectnew ? fbxObjectnew : CPA3DfbxObject;

    if (typeof (toggleSize_Override) == 'function') {
        return toggleSize_Override(width, widthMin, widthMax, height, heightMin, heightMax, depth, depthMin, depthMax, fbxObjectnew);
    }

    const MIN_SCALE = 0.1;
    const MAX_SCALE = 0.15;

    // aplicar fallback
    width = safe(width);
    widthMin = safe(widthMin);
    widthMax = safe(widthMax);

    height = safe(height);
    heightMin = safe(heightMin);
    heightMax = safe(heightMax);

    depth = safe(depth);
    depthMin = safe(depthMin);
    depthMax = safe(depthMax);

    let xalt = mapRange(width, widthMin, widthMax, MIN_SCALE, MAX_SCALE);
    let yalt = mapRange(height, heightMin, heightMax, MIN_SCALE, MAX_SCALE);
    let zalt = mapRange(depth, depthMin, depthMax, MIN_SCALE, MAX_SCALE);

    fbxObjectchange.scale.set(xalt, yalt, zalt);
}