class ModelManager {

    constructor(viewer, loader) {

        this.viewer = viewer;
        this.scene = viewer.scene;
        this.camera = viewer.camera;
        this.controls = viewer.controls;
        this.loader = loader;
        this.objects = [];


        this.materialConfig = {
            color: 0xffffff,
            metalness: 0.7,
            roughness: 0.35
        };

        this.position = {
            x: 0,
            y: 0,
            z: 0
        };

        this.scale = {
            x: 1,
            y: 1,
            z: 1
        };

        this.count = 1;
        this.layoutFn = null;
        this.spacing = 0;
        this.path = "";
        this.file = "";
    }

    async load(path) {

        return new Promise((resolve, reject) => {

            this.loader.load(
                path,
                resolve,
                undefined,
                reject
            );

        });
    }

    async addModel(path, options = {}) {

        const object = await this.load(path);

        const x = options.x || 0;
        const y = options.y || 0;
        const z = options.z || 0;

        object.position.set(x, y, z);

        object.scale.set(
            options.scaleX || this.scale.x,
            options.scaleY || this.scale.y,
            options.scaleZ || this.scale.z
        );

        this.setupObject(object);
        this.scene.add(object);
        this.objects.push(object);
        this.fitCamera(this.objects);

        return object;
    }

    setupObject(object) {

        object.traverse((child) => {
            if (child.isMesh) {
                child.material = new THREE.MeshStandardMaterial({
                    color: this.materialConfig.color,
                    metalness: this.materialConfig.metalness,
                    roughness: this.materialConfig.roughness
                });
            }
        });

    }

    // setLayout(fn) {
    //     this.layoutFn = fn;
    //     return this;
    // }

    // setModel(path, file) {

    //     this.path = path;
    //     this.file = file;

    //     return this;
    // }

    fitCamera(objects) {

        const group = new THREE.Group();

        if (Array.isArray(objects)) {

            objects.forEach(obj => {
                group.add(obj);
            });

        } else {

            group.add(objects);
        }

        const box = new THREE.Box3()
            .setFromObject(group);

        const size = new THREE.Vector3();
        const center = new THREE.Vector3();

        box.getSize(size);
        box.getCenter(center);

        const maxDim = Math.max(
            size.x,
            size.y,
            size.z
        );

        const fov =
            this.camera.fov *
            (Math.PI / 180);

        let cameraZ =
            Math.abs(
                maxDim / 2 /
                Math.tan(fov / 2)
            );

        cameraZ *= 1.5;

        this.camera.position.set(
            center.x,
            center.y,
            center.z + cameraZ
        );

        this.controls.target.copy(center);

        this.controls.update();
    }


    setColor(color) {

        this.materialConfig.color = color;

        this.objects.forEach(obj => {
            obj.traverse(child => {
                if (child.isMesh) {
                    child.material.color.set(color);
                }
            });
        });

        return this;
    }

    // setCount(count) {
    //     this.count = count;
    //     return this;
    // }

    setScale(x, y, z) {
        this.scale = { x, y, z };

        this.objects.forEach(obj => {
            obj.scale.set(x, y, z);
        });

        return this;
    }

    setPosition(x, y, z) {

        this.position = { x, y, z };

        this.objects.forEach(obj => {
            obj.position.set(x, y, z);
        });

        return this;
    }

    setMetalness(value) {

        this.materialConfig.metalness = value;

        return this;
    }

    setRoughness(value) {

        this.materialConfig.roughness = value;

        return this;
    }

    clear() {
        this.objects.forEach(obj => {
            this.scene.remove(obj);
            obj.traverse(child => {
                if (child.isMesh) {
                    child.geometry.dispose();
                    child.material.dispose();
                }
            });
        });

        this.objects = [];
    }

    // async reload() {

    //     this.clear();

    //     const base = await this.load(
    //         this.path + this.file
    //     );

    //     this.objects = [];

    //     for (let i = 0; i < this.count; i++) {

    //         const obj = base.clone(true);

    //         if (this.layoutFn) {
    //             const pos = this.layoutFn(i, this.count, obj);
    //             obj.position.set(pos.x, pos.y, pos.z);
    //         } else {
    //             obj.position.set(
    //                 this.position.x + (i * this.spacing),
    //                 this.position.y,
    //                 this.position.z
    //             );
    //         }

    //         this.setupObject(obj);
    //         this.scene.add(obj);
    //         this.objects.push(obj);
    //     }

    //     this.fitCamera(this.objects);
    // }
}