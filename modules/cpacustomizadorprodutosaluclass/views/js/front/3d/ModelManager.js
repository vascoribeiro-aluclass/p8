class ModelManager {
    constructor(viewer, loader) {
        this.viewer = viewer;
        this.scene = viewer.scene;
        this.camera = viewer.camera;
        this.controls = viewer.controls;
        this.loader = loader;

        this.extraModal = 0;

        this.objects = [];

        this.materialConfig = {
            color: 0xffffff,
            metalness: 0.7,
            roughness: 0.35,
        };

        this.position = {
            x: 0,
            y: 0,
            z: 0,
        };

        this.scale = {
            x: 1,
            y: 1,
            z: 1,
        };
    }

    /*LOAD*/
    async load(path) {
        return new Promise((resolve, reject) => {
            this.loader.load(path, resolve, undefined, reject);
        });
    }

    /*Adicionar modelo à cena*/
    async addModel(path, options = {}) {
        const object = await this.load(path);

        object.userData.id = options.id || crypto.randomUUID();

        object.position.set(
            options.positionx ?? this.position.x,
            options.positiony ?? this.position.y,
            options.positionz ?? this.position.z
        );

        object.scale.set(
            options.scaleX ?? this.scale.x,
            options.scaleY ?? this.scale.y,
            options.scaleZ ?? this.scale.z,
        );

        this.setupObject(object, options.meshName ?? '', options.materialOptions ?? {});
        this.scene.add(object);
        this.objects.push(object);
        this.fitCamera(this.objects);

        return object;
    }

    /* MATERIALS*/
    setupObject(object, meshName = '', materialOptions = {}) {
        object.traverse((child) => {
            if (child.isMesh) {
                if (child.name.toLowerCase().includes(meshName.toLowerCase())) {
                    child.material = new THREE.MeshStandardMaterial({
                        color: materialOptions.color ?? this.materialConfig.color,
                        metalness: materialOptions.metalness ?? this.materialConfig.metalness,
                        roughness: materialOptions.roughness ?? this.materialConfig.roughness,
                    });
             
                    child.material.needsUpdate = true;
                } else {
                    child.material = new THREE.MeshStandardMaterial({
                        color: this.materialConfig.color,
                        metalness: this.materialConfig.metalness,
                        roughness: this.materialConfig.roughness,
                    });
                      child.material.needsUpdate = true;
                }
            }
        });
    }

    /* FIT CAMERA*/
    fitCamera(objects) {
        const box = new THREE.Box3();

        if (Array.isArray(objects)) {
            objects.forEach((obj) => {
                box.expandByObject(obj);
            });
        } else {
            box.expandByObject(objects);
        }

        const size = new THREE.Vector3();
        const center = new THREE.Vector3();

        box.getSize(size);
        box.getCenter(center);

        /*Distancia da camera*/
        const maxDim = Math.max(size.x, size.y, size.z);
        const fov = this.camera.fov * (Math.PI / 180);

        let cameraZ = Math.abs(maxDim / 2 / Math.tan(fov / 2));
        cameraZ *= 1.8;
        this.camera.position.set(center.x, center.y, center.z + cameraZ);

        /*centralizar*/
        this.controls.target.copy(center);
        this.camera.near = 0.1;
        this.camera.far = cameraZ * 10;
        this.camera.updateProjectionMatrix();
        this.controls.minDistance = 100;
        this.controls.maxDistance = 1000;
        this.controls.update();
    }

    setColor(color) {
        this.materialConfig.color = color;
        this.objects.forEach((obj) => {
            obj.traverse((child) => {
                if (child.isMesh) {
                    child.material.color.set(color);
                }
            });
        });

        return this;
    }

    setScale(x, y, z) {
        this.scale = { x, y, z };
        this.objects.forEach((obj) => {
            obj.scale.set(x, y, z);
        });

        return this;
    }

    setPosition(x, y, z) {
        this.position = { x, y, z };
        this.objects.forEach((obj) => {
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

    removeModel(id) {

        const index = this.objects.findIndex(
            obj => obj.userData.id === id
        );

        if (index === -1) {
            console.warn("Objeto não encontrado:", id);
            return;
        }

        const obj = this.objects[index];
        this.scene.remove(obj);

        obj.traverse(child => {
            if (child.isMesh) {
                child.geometry.dispose();
                if (child.material) {
                    if (Array.isArray(child.material)) {
                        child.material.forEach(m => m.dispose());
                    } else {
                        child.material.dispose();
                    }
                }
            }
        });

        this.objects.splice(index, 1);
        this.fitCamera(this.objects);
    }


    clear() {
        this.objects.forEach((obj) => {
            this.scene.remove(obj);

            obj.traverse((child) => {
                if (child.isMesh) {
                    if (child.geometry) {
                        child.geometry.dispose();
                    }

                    if (child.material) {
                        if (Array.isArray(child.material)) {
                            child.material.forEach((material) => material.dispose());
                        } else {
                            child.material.dispose();
                        }
                    }
                }
            });
        });

        this.objects = [];
    }
}
