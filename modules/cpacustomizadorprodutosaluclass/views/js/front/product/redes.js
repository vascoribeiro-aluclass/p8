// Ficheiro JS criado automaticamente

const models = new ModelManager(CPA3Dscene, CPA3Dloader, modulePath + "views/js/front/3d/product/", "48485-1.fbx");

// configurar
models
    .setCount(0)
    .setSpacing(253)
    .setPosition(253, -100, -12);


function toggleMaterial_Override(color, fbxObjectnew) {

    let fbxObjectchange = fbxObjectnew ? fbxObjectnew : CPA3DfbxObject;

    models
        .setColor(color)
        .setMetalness(0.7)
        .setRoughness(0.35)
        .reload();

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

function toggleSize_Override(width, widthMin, widthMax, height, heightMin, heightMax, depth, depthMin, depthMax, fbxObjectnew) {
    models.setCount(width);
    models.setColor(CPA3Dcolor);
    models.setMetalness(0.7);
    models.setRoughness(0.35);
    models.reload();

    const group = new THREE.Group();
    models.objects.forEach(obj => group.add(obj));

    const box = new THREE.Box3().setFromObject(group);
    const center = box.getCenter(new THREE.Vector3());
    CPA3Dcamera.lookAt(center);
    CPA3Dcamera.position.set(center.x, center.y, center.z + (width*1000));
    CPA3Dcontrols.target.set((width*125), 0,0 );

     setupBackground(CPA3Dimagens[CPA3DindexFundo], (width*125));

}