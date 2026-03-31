if (!Detector.webgl) Detector.addGetWebGLMessage();

var controls;
var camera, scene, renderer;
var fbxObject;

function init(color) {
    // Seleciona a div #3dshow existente
    container = document.getElementById("3dshow");

    if (!container) {
        console.error("Div #3dshow não encontrada!");
        return;
    }

    // Cria a cena
    scene = new THREE.Scene();
    scene.background = new THREE.Color(0x383E42);

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
    const geometry = new THREE.SphereGeometry(150, 60, 40);
    geometry.scale(-1, 1, 1);
    const material = new THREE.MeshBasicMaterial({
        map: new THREE.TextureLoader().load(modulePath + "views/js/front/3d/BGCompleto_360.jpg"),
    });

    const mesh = new THREE.Mesh(geometry, material);
    mesh.position.y += 5;
    scene.add(mesh);

    // Loader do FBX
    const loader = new THREE.FBXLoader();
    loader.load(modulePath + "views/js/front/3d/product/" + name3dshow, function (object) {
        fbxObject = object;
        fbxObject.scale.set(0.01, 0.01, 0.01);
        fbxObject.position.y -= 12;
        fbxObject.position.z -= 12;

        if (color) {
            if (typeof color === "string" && !color.startsWith('#')) 
                color = '#' + color;
            toggleMaterial(color);
        }

        // Centraliza a câmera
        const boundingBox = new THREE.Box3().setFromObject(fbxObject);
        const middle = new THREE.Vector3();
        const size = new THREE.Vector3();
        boundingBox.getCenter(middle);
        boundingBox.getSize(size);

        const offset = 1.25;
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
    toggleMaterial(color);
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
