if (!Detector.webgl) Detector.addGetWebGLMessage();

var controls;
var camera, scene, renderer;
var fbxObject;
var backgroundMesh;
var dynamicRadius;
var indexFundo = 0;
var imagens = ["BGCompleto_360.jpg", "img360.jpg", "Piso_360.jpg" ];

function changeBackground() {
    indexFundo = (indexFundo + 1) % imagens.length;
    setupBackground( imagens[indexFundo]);
}

function init(color) {
    // Seleciona a div #3dshow existente
    container = document.getElementById("3dshow");

    if (!container) {
        console.error("Div #3dshow não encontrada!");
        return;
    }

    // Cria a cena
    scene = new THREE.Scene();
    scene.background = new THREE.Color(0x89d0ff);

    // Câmera
    camera = new THREE.PerspectiveCamera(
        75,
        container.clientWidth / container.clientHeight, // tamanho do container
        0.1,
        5000
    );

    // Luzes
    scene.add(new THREE.AmbientLight(0xffffff, 0.2));

    const hemisphereLight = new THREE.HemisphereLight(0xffffff, 0x444444);
    hemisphereLight.position.set(0, 1, 0);
    scene.add(hemisphereLight);

    const directionalLight = new THREE.DirectionalLight(0xffffff, 0.5);
    directionalLight.position.set(0, 4, 4);
    scene.add(directionalLight);

    // Controles
    controls = new THREE.OrbitControls(camera, container);
    controls.addEventListener("change", render);
    controls.enableDamping = true;
    controls.dampingFactor = 0.25;
    controls.enableZoom = true;
    controls.minPolarAngle = 0;
    controls.maxPolarAngle = Math.PI * 0.55;

    // Background esférico
    // const geometry = new THREE.SphereGeometry(150, 60, 40);
    // geometry.scale(-1, 1, 1);
    // const material = new THREE.MeshBasicMaterial({
    //     map: new THREE.TextureLoader().load(modulePath + "views/js/front/3d/BGCompleto_360.jpg"),
    // });

    // const mesh = new THREE.Mesh(geometry, material);
    // mesh.position.y += 5;
    // scene.add(mesh);

    // Loader do FBX
    const loader = new THREE.FBXLoader();
    loader.load(modulePath + "views/js/front/3d/product/" + name3dshow, function (object) {
        fbxObject = object;
        fbxObject.scale.set(0.1, 0.1, 0.1);
        fbxObject.position.y -= 100;
        fbxObject.position.z -= 12;

        if (color) {
            if (typeof color === "string" && !color.startsWith('#'))
                color = '#' + color;
            toggleMaterial(color);
        }

        // Centraliza a câmera
        const boundingBox = new THREE.Box3().setFromObject(fbxObject);
        const size = new THREE.Vector3();
        boundingBox.getSize(size);

        const maxDim = Math.max(size.x, size.y, size.z);
        dynamicRadius = maxDim * 10;

        setupBackground( "BGCompleto_360.jpg");

        const offset = 2;
        const fov = camera.fov * (Math.PI / 180);
        const fovh = 2 * Math.atan(Math.tan(fov / 2) * camera.aspect);
        let dx = size.z / 2 + Math.abs(size.x / 2 / Math.tan(fovh / 2));
        let dy = size.z / 2 + Math.abs(size.y / 2 / Math.tan(fov / 2));
        let cameraZ = Math.max(dx, dy);
        if (offset) cameraZ *= offset;

        camera.position.set(0, 0, cameraZ);

        const minZ = boundingBox.min.z;
        const cameraToFarEdge = minZ < 0 ? -minZ + cameraZ : cameraZ - minZ;
        controls.maxDistance = cameraToFarEdge * 2;
        controls.target.set(0, 0, fbxObject.position.z);

        scene.add(fbxObject);
    },
        function (xhr) {
            console.log((xhr.loaded / xhr.total * 100) + "% carregado");
        }, function (error) {
            console.error("Erro ao carregar FBX:", error);
        });



    // Renderer
    renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setPixelRatio(window.devicePixelRatio);
    renderer.setSize(container.clientWidth, container.clientHeight);
    container.appendChild(renderer.domElement);

    // Redimensionamento
    window.addEventListener("resize", onWindowResize, false);
    // toggleMaterial(color);
}

function onWindowResize() {
    if (!container) return;
    camera.aspect = container.clientWidth / container.clientHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(container.clientWidth, container.clientHeight);
    render();

}

function animate() {
    requestAnimationFrame(animate);
    controls.update();
    render();
}

function render() {
    renderer.render(scene, camera);
}

function setupBackground( imageName) {
    if (backgroundMesh) {
        backgroundMesh.geometry.dispose();
    }
    const geometry = new THREE.SphereGeometry(dynamicRadius, 60, 40);
    geometry.scale(-1, 1, 1);
    const material = new THREE.MeshBasicMaterial({
        map: new THREE.TextureLoader().load(modulePath + "views/js/front/3d/" + imageName),
    });

    backgroundMesh = new THREE.Mesh(geometry, material);
    backgroundMesh.position.y += 5;
    scene.add(backgroundMesh);
     backgroundMesh.material.needsUpdate = true;
    render();
}

// function toggleMaterial(color) {
//     if (!fbxObject) return;

//     let lamParts = [];

//     fbxObject.traverse((child) => {
//         if (child.isMesh && child.name.startsWith("lam")) {
//             lamParts.push(child);
//         }
//     });

//     // depois:

//     lamParts.forEach(part => {
//                 part.material = new THREE.MeshStandardMaterial({
//             color: color,
//             metalness: 0.5,
//             roughness: 0.5
//         });
//         part.material.needsUpdate = true;
//     });



// }

function toggleMaterial(color) {
    if (!fbxObject) return;


    fbxObject.traverse(function (child) {
        if (child.isMesh) {
            child.material = new THREE.MeshStandardMaterial({
                color: color,
                metalness: 0.5,
                roughness: 0.5
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

function toggleSize(width, widthMin, widthMax, height, heightMin, heightMax, depth, depthMin, depthMax) {
    if (!fbxObject) return;

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


    fbxObject.scale.set(xalt, yalt, zalt);
}