<?php
    require '../../includes/funciones.php';
    $auth=estaAutenticado();
    if(!$auth){
        header('location:/');
    }
    //VALIDAR id VÁLIDO
    $id=$_GET['id'];
    $id=filter_var($id,FILTER_VALIDATE_INT);

    if(!$id){
        header('location:/admin');
    }
    
    //Base de datos
    require '../../includes/config/database.php';
    $db=conectarDB();

    //Obtener los datos de la propiedad
    $consulta="SELECT *FROM propiedades where id={$id}";
    $resultado=mysqli_query($db,$consulta);
    $propiedad=mysqli_fetch_assoc($resultado);

    //Consultar para obtener los vendeodores
    $consulta="SELECT * FROM vendedores";
    $resultado_consulta=mysqli_query($db,$consulta);

    //Arreglo con mensajes de errores
    $errores=[];
    //Inicializamos las variables
    $titulo=$propiedad['titulo'];
    $precio=$propiedad['precio'];
    $imagenPropiedad=$propiedad['imagen'];
    $descripcion=$propiedad['descripcion'];
    $habitaciones=$propiedad['habitaciones'];
    $wc=$propiedad['wc'];
    $estacionamiento=$propiedad['estacionamiento'];
    $vendedorId=$propiedad['vendedorId'];
    //Ejecutar el codigo despues que el usuario envia el formulario
    if($_SERVER["REQUEST_METHOD"]=='POST'){

        echo "<pre>";
        var_dump($_POST);
        echo "</pre>";
        /*echo "<pre>";
        var_dump($_FILES);
        echo "</pre>";*/

        //Sanitización
        $titulo=mysqli_real_escape_string($db,$_POST["titulo"]);
        $precio=mysqli_real_escape_string($db,$_POST["precio"]);
        $descripcion=mysqli_real_escape_string($db,$_POST["descripcion"]);
        $habitaciones=mysqli_real_escape_string($db,$_POST["habitaciones"]);
        $wc=mysqli_real_escape_string($db,$_POST["wc"]);
        $estacionamiento=mysqli_real_escape_string($db,$_POST["estacionamiento"]);
        $vendedorId=mysqli_real_escape_string($db,$_POST["vendedor"]);
        $creado=date('Y/m/d');

        //Asignar files hacia una variable
        $imagen=$_FILES['imagen'];

        if(!$titulo){
            $errores[]="Debes añadir un título";
        }

        if(!$precio){
            $errores[]="El precio es obligatorio";
        }

        if(strlen($descripcion)<50){
            $errores[]="La descripción es obligatoria y debe tener al menos 50 caracteres";
        }

        if(!$habitaciones){
            $errores[]="El numero de habitaciones es obligatorio";
        }

        if(!$wc){
            $errores[]="El numero de baños es obligatorio";
        }

        if(!$estacionamiento){
            $errores[]="El numero de lugares de estacionamiento es obligatorio";
        }

        if(!$vendedorId){
            $errores[]="Elije un vendedor ";
        }
        

        //Validar por tamaño(1MB maximo)
        $medida=1000 * 1000; 
        if($imagen['size']>$medida){
            $errores[]="La imagen es muy pesada";
        }

        //Revisar que el arreglo de errores este vacío

        if(empty($errores)){

            /**Crear Carpeta*/
            $carpetaImagenes='../../imagenes/';
            if(!is_dir($carpetaImagenes)){
                mkdir($carpetaImagenes);
            }

            $nombreImagen='';

            /**Subida de archivos**/
            if($imagen['name']){
                //Eliminar la imagen previa
                unlink($carpetaImagenes.$propiedad['imagen']);

                //Generar un nombre unico
                $nombreImagen=md5(uniqid(rand(),true))."jpg";

                //Subir la imagen 
                move_uploaded_file($imagen['tmp_name'],$carpetaImagenes.$nombreImagen);
            }else{
                $nombreImagen=$propiedad['imagen'];
            }


          //Actualizar en la base de datos
            $query="UPDATE propiedades set titulo='{$titulo}', precio='{$precio}',imagen='{$nombreImagen}',descripcion='{$descripcion}', habitaciones={$habitaciones},wc={$wc},estacionamiento={$estacionamiento},vendedorId={$vendedorId} where id={$id}";

            //ECHO $query;
            
            $resultado=mysqli_query($db,$query);
            if($resultado){
                //Redireccionar al usuario cuando ya se registro
                header('location:/admin?resultado=2');
            } 
        }   
    }

    
    incluirTemplate('header')
?>
    <main class="contenedor seccion">
        <h1>Actualizar Propiedad</h1>
        <a href="/admin" class="boton boton-verde">Volver</a>
        <?php foreach($errores as $error): ?>
            <div class="alerta error">
                <?php echo $error; ?>
            </div>
        <?php endforeach ?>
        <form class="formulario" method="POST" enctype="multipart/form-data">
            <fieldset>
                <legend>Informacion General</legend>
                <label for="titulo">Titulo:</label>
                <input type="text" placeholder="Titulo Propiedad" id="titulo" name="titulo" value="<?php echo $titulo ?>">

                <label for="precio">Precio:</label>
                <input type="number" placeholder="Precio Propiedad" id="precio" name="precio" value="<?php echo $precio ?>">

                <label for="imagen">Imagen:</label>
                <input type="file"  id="imagen" accept="image/jpeg, image/png" name="imagen">

                <img src="/imagenes/<?php echo $imagenPropiedad ?>" class="imagen-pequeña">

                <label for="descripcion">Descripcion:</label>
                <textarea id="descripcion" name="descripcion"><?php echo $descripcion?></textarea>
            </fieldset>

            <fieldset>
                <legend>Información Propiedad</legend>

                <label for="habitaciones">Habitaciones:</label>
                <input type="number"  id="habitaciones" placeholder="Ej: 3" min="1" max="9" name="habitaciones" value="<?php echo $habitaciones ?>">

                <label for="wc">Baños:</label>
                <input type="number"  id="wc" placeholder="Ej: 3" min="1" max="9" name="wc" value="<?php echo $wc ?>">

                <label for="estacionamiento">Estacionamiento:</label>
                <input type="number"  id="estacionamiento" placeholder="Ej: 3" min="1" max="9" name="estacionamiento" value="<?php echo $estacionamiento ?>">
            </fieldset>

            <fieldset>
                <legend>Vendedor</legend>
                <select name="vendedor" value="<?php echo $vendedorId ?>">
                    <option value="">--Seleccione--</option>
                    <?php while($vendedor=mysqli_fetch_assoc($resultado_consulta)):?>
                        <option <?php echo $vendedorId==$vendedor['id'] ? 'selected' : ''; ?> value="<?php echo $vendedor['id']?>"><?php echo $vendedor['nombre']." ".$vendedor['apellido'] ?></option>
                        <?php endwhile; ?>
                </select>
            </fieldset>
            <input type="submit" value="Actualizar Propiedad" class="boton boton-verde">
        </form>
    </main>

<?php
    incluirTemplate('footer');
?>