## PROCEDIMIENTO DE ACTUALIZACION

A tomar en cuenta:
* El facturador se encontrará desplegado mediante al menos 5 contenedores de Docker
* Para actualizar se requiere ingresar solo a uno de ellos

a. ejecutar `docker ps`
b. Aparecera un listado con los contenedores activos, debemos identificar el contenedor mediante el **COMMAND** con valor `php-fpm7.2` o similar
c. copiar el primer valor de la linea equivalente a **CONTAINER ID**
d. ejecutar `docker exec -ti codigodelcontenedor bash`
e. una vez ingresado al contenedor, ejecutar `git pull origin master`
f. ingresar las credenciales solicitadas (correo y contraseña)
g. ejecutar `php artisan migrate && php artisan tenancy:migrate`
h. ejecutar `php artisan config:cache && php artisan cache:clear && php artisan optimize:clear`
