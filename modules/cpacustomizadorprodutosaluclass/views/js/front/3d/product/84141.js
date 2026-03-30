if (!Detector.webgl) Detector.addGetWebGLMessage();

let container, stats, controls;
let camera, scene, renderer, light;
let fbxObject;




function init() {
    container = document.createElement("div");
    document.body.appendChild(container);

    camera = new THREE.PerspectiveCamera(
        75,
        window.innerWidth / window.innerHeight,
        0.1,
        5000,
    );

    scene = new THREE.Scene();
    scene.background = new THREE.Color(0xffffff);

    scene.add(new THREE.AmbientLight(0xffffff, 0.2));

    var hemisphereLight = new THREE.HemisphereLight(0xffffff, 0x444444);
    hemisphereLight.position.set(0, 1, 0);
    scene.add(hemisphereLight);

    var directionalLight = new THREE.DirectionalLight(0xffffff, 0.5);
    directionalLight.position.set(0, 4, 4);
    scene.add(directionalLight);

    controls = new THREE.OrbitControls(camera);
    controls.addEventListener("change", render);
    controls.enableDamping = true;
    controls.dampingFactor = 0.25;
    controls.enableZoom = true;
    controls.minPolarAngle = 0;
    controls.maxPolarAngle = Math.PI * 0.55;

    // Background esférico
    var geometry = new THREE.SphereGeometry(150, 60, 40);
    geometry.scale(-1, 1, 1);
    var material = new THREE.MeshBasicMaterial({
        map: new THREE.TextureLoader().load("images/BGCompleto_360.jpg"),
    });
    var mesh = new THREE.Mesh(geometry, material);
    mesh.position.y += 5;
    scene.add(mesh);

    // Loader do FBX
    var loader = new THREE.FBXLoader();
    loader.load("84141.fbx", function (object) {
        fbxObject = object;
        object.scale.set(0.01, 0.01, 0.01);
        object.position.y -= 12;
        object.position.z -= 12;

        // Centraliza a câmera em relação ao objeto
        const boundingBox = new THREE.Box3().setFromObject(object);
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
        if (offset !== undefined && offset !== 0) cameraZ *= offset;

        camera.position.set(0, 0, cameraZ);

        const minZ = boundingBox.min.z;
        const cameraToFarEdge = minZ < 0 ? -minZ + cameraZ : cameraZ - minZ;
        controls.maxDistance = cameraToFarEdge * 2;
        controls.target.set(0, 0, object.position.z);

        scene.add(object);
    });

    renderer = new THREE.WebGLRenderer({ antialias: true });
    renderer.setSize(window.innerWidth, window.innerHeight);
    renderer.setPixelRatio(window.devicePixelRatio);
    container.appendChild(renderer.domElement);

    window.addEventListener("resize", onWindowResize, false);
}

function toggleMaterial(color) {
    if (!fbxObject) return;
    fbxObject.traverse(function (child) {
        if (child.isMesh) {
            child.material = new THREE.MeshStandardMaterial({
                color: color,
                metalness: 0.5,
                roughness: 0.5,
            });
            child.material.needsUpdate = true;
        }
    });
}

function onWindowResize() {
    camera.aspect = window.innerWidth / window.innerHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(window.innerWidth, window.innerHeight);
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

$(document).ready(function () {
    init();
    animate();
    $('.img-value.is_visual').on('click', function () {
        var color = $(this).data('color');
        toggleMaterial(color);
    });
});