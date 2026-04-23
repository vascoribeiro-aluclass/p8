


class ModelManager {
    constructor(scene, loader, path, file) {
        this.scene = scene;
        this.loader = loader;
        this.path = path;
        this.file = file;

        this.objects = [];
        this.loadId = 0;
        this.count = 0;
        this.spacing = 0;
        this.position = { x: 0, y: 0, z: 0 };
        this.metalness = 0.7;
        this.roughness = 0.35;
        this.color = 0xffffff;

    }

    setCount(count) {
        this.count = count;
        return this;
    }

    setSpacing(spacing) {
        this.spacing = spacing;
        return this;
    }

    setPosition(x, y, z) {
        this.position = { x, y, z };
        return this;
    }

    setFile(file) {
        this.file = file;
        return this;
    }
    setMetalness(metalness) {
        this.metalness = metalness;
        return this;
    }

    setRoughness(roughness) {
        this.roughness = roughness;
        return this;
    }

    setColor(color) {
        this.color = color;
        return this;
    }

    material(obj) {
        obj.traverse((child) => {
            if (child.isMesh) {
                child.material = new THREE.MeshStandardMaterial({
                    color: this.color,
                    metalness: this.metalness,
                    roughness: this.roughness
                });
                child.material.needsUpdate = true;
            }
        });
    }

    clear() {
        this.objects.forEach(obj => {
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
        });

        this.objects = [];
    }

    reload() {
        const currentLoad = ++this.loadId;

        const objectsToClear = this.objects;
        this.objects = [];

        objectsToClear.forEach(obj => {
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
        });

        for (let i = 0; i < this.count; i++) {
            this.loader.load(this.path + this.file, (obj) => {

                if (currentLoad !== this.loadId) return;

                obj.position.set(
                    this.position.x + this.spacing * i,
                    this.position.y,
                    this.position.z
                );

                this.material(obj);

                this.scene.add(obj);
                this.objects.push(obj);
            });
        }
    }
}
