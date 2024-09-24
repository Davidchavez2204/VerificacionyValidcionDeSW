<?php
    //Importar la conexion
    //OJO LLAMAMOS A ESTE ARCHIVO DESDE INDEX.PHP POR ESO LO PONEMOS ASI, rquiere es relativo al documento que lo manda a llamar.
    $db=conectarDB();

    //Consultar
    $query="SELECT * FROM propiedades limit {$limite}";

    //Obtener Resultados
    $resultado=mysqli_query($db,$query); 
    
    if($resultado->num_rows===0){
        header('location:/');
    }
?>

<div class="contenedor-anuncios">
    <?php while($propiedad=mysqli_fetch_assoc($resultado)): ?>
            <div class="anuncio">
                    <img loading="lazy" src="/imagenes/<?php echo $propiedad['imagen'];?>" alt="anuncio">
                <div
                    class="contenido-anuncio">
                    <h3><?php echo $propiedad['titulo'];?></h3>
                    <p class="descripcion"><?php echo $propiedad['descripcion'];?></p>
                    <p class="precio">$ <?php echo $propiedad['precio'];?></p>
                    <ul class="iconos-caracteristicas">
                        <li>
                            <img class="
                            icono" loading="lazy" src="../../build/img/icono_wc.svg" alt="icono wc">
                            <p><?php echo $propiedad['wc'];?></p>
                        </li>
                        <li>
                            <img class="
                            icono" loading="lazy" src="../../build/img/icono_estacionamiento.svg" alt="icono estacionamiento">
                            <p><?php echo $propiedad['estacionamiento'];?></p>
                        </li>
                        <li>
                            <img class="
                            icono" loading="lazy" src="../../build/img/icono_estacionamiento.svg" alt="icono habitacones">
                            <p><?php echo $propiedad['habitaciones'];?></p>
                        </li>
                    </ul>
                    <a href="anuncio.php?id=<?php echo $propiedad['id'];?>" class="boton boton-amarillo-block">
                        Ver Propiedad
                    </a>
                </div>
            </div>
            <?php endwhile;?>
        </div>
<?php
//Cerrar la conexion
mysqli_close($db);
?>