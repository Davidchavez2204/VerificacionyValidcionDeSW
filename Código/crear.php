<?php
    require '../../includes/app.php';

    use App\Propiedad;
    use Intervention\Image\ImageManagerStatic as Image;

    estaAutenticado();
    $propiedad=new Propiedad;
    
    //Base de datos
    $db=conectarDB();

    //Consultar para obtener los vendeodores
    $consulta="SELECT * FROM vendedores";
    $resultado_consulta=mysqli_query($db,$consulta);

    //Arreglo con mensajes de errores
    $errores=Propiedad::getErrores(); 

    //Ejecutar el codigo despues que el usuario envia el formulario
    if($_SERVER["REQUEST_METHOD"]=='POST'){

        //Crea una nueva Instancia
        $propiedad=new Propiedad($_POST);

        /**Subida de archivos**/
        //Generar un nombre unico
        $nombreImagen=md5(uniqid(rand(),TRUE))."jpg";

        //Setear la imagen
        //Realiza un resize a la imagen con Intervention
        if($_FILES['imagen']['tmp_name']){
            $image= Image::make($_FILES['imagen']['tmp_name'])->fit(800,600);
            $propiedad->setImagen($nombreImagen);
        }
        
        //Validar
        $errores=$propiedad->validar();

        //Revisar que el arreglo de errores este vacío

        if(empty($errores)){
            if(!is_dir(CARPETA_IMAGENES)){
                mkdir(CARPETA_IMAGENES);
            }

            //Guarda la imagen en el servidor
            $image->save(CARPETA_IMAGENES.$nombreImagen);
        
            //Guarda en la base de datos
            $resultado=$propiedad->guardar();
            //Mensaje de exito o error
            if($resultado){
                //Redireccionar al usuario cuando ya se registro
                header('location:/admin?resultado=1');
        }  
        }  
    }

    //Incluye un template
    incluirTemplate('header')
?>
    <main class="contenedor seccion">
        <h1>Crear</h1>
        <a href="/admin" class="boton boton-verde">Volver</a>
        <?php foreach($errores as $error): ?>
            <div class="alerta error">
                <?php echo $error; ?>
            </div>
        <?php endforeach ?>
        <form class="formulario" method="POST" action="/admin/propiedades/crear.php" enctype="multipart/form-data">
            <?php include '../../includes/templates/formulario_propiedades.php' ?>
            <input type="submit" value="Crear Propiedad" class="boton boton-verde">
        </form>
    </main>

<?php
    incluirTemplate('footer');
?>