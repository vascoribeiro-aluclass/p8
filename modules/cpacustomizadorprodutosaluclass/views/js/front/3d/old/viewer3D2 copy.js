class Viewer3D {
    constructor(containerId) {

        this.container = document.getElementById(containerId);
        this.scene = new THREE.Scene();

        this.camera = new THREE.PerspectiveCamera( 75,  this.container.clientWidth / this.container.clientHeight, 0.1, 5000 );

        this.renderer = new THREE.WebGLRenderer({ antialias: true  });
        this.renderer.setPixelRatio(window.devicePixelRatio);
        this.renderer.setSize( this.container.clientWidth, this.container.clientHeight );

        this.container.appendChild(this.renderer.domElement);

        this.controls = new THREE.OrbitControls(this.camera, this.renderer.domElement );
        this.controls.enableDamping = true;

        this.backgroundMesh = null;
       

        this.setupLights();
        this.animate();

        window.addEventListener("resize",() => this.onResize());
    }

    setBackground(imagePath) {

        const geometry = new THREE.SphereGeometry(2000,30,20);

        geometry.scale(-1, 1, 1);

        const texture = new THREE.TextureLoader()
            .load(imagePath);

        const material = new THREE.MeshBasicMaterial({ map: texture});

        if (this.backgroundMesh) {
            this.scene.remove(this.backgroundMesh);
            this.backgroundMesh.geometry.dispose();
            this.backgroundMesh.material.map.dispose();
            this.backgroundMesh.material.dispose();
        }

        this.backgroundMesh = new THREE.Mesh( geometry, material);

        this.scene.add(this.backgroundMesh);
    }


    setupLights() {

        this.scene.add(
            new THREE.AmbientLight(0xffffff, 1.2)
        );

        const hemi = new THREE.HemisphereLight( 0xffffff,0xdddddd, 1.5);

        this.scene.add(hemi);

        const dir = new THREE.DirectionalLight( 0xffffff, 1.0 );

        dir.position.set(5, 10, 5);
        this.scene.add(dir);
    }

    onResize() {
        this.camera.aspect =this.container.clientWidth / this.container.clientHeight;
        this.camera.updateProjectionMatrix();
        this.renderer.setSize( this.container.clientWidth, this.container.clientHeight );
    }

    animate() {

        requestAnimationFrame(
            () => this.animate()
        );

        this.controls.update();
        this.renderer.render( this.scene, this.camera );
    }
}