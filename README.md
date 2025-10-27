# ERUS DNA Mutation
## Descripción del proyecto
Este es un buscador de mutaciones en secuencias de ADN realizado con PHP/Laravel y Angular. Se muestra una comparativa entre registros con y sin mutaciones, además de un listado de los últimos 10 registros.
## ¿Por qué este proyecto?
Este proyecto fue creado como un reto integral para aprender y practicar Laravel y Angular. 
## Requerimientos
Para ejecutar como cliente de forma local solo necesitarás `Node.js` y `Angular`
* [Node.js](https://nodejs.org/es)
* [Angular CLI](https://angular.dev/installation)
  
En caso de ejecutar ambas partes de forma local necesitaras `Composer` y `Laravel`
* [Composer](https://getcomposer.org/download/)
* [Laravel](https://laravel.com/)
### Recomendaciones
- Para instalar `Composer` debes tener la extensión `zip` en `PHP`.
### Instalación y ejecución
Clona este repositorio en tu máquina local
```
git clone https://github.com/fungirox/DNA-mutation.git
```
Navega hasta el directorio del proyecto
```
cd dna-mutation-client
```
Ejecuta el programa
```
npm start
```
Por defecto, usarás el puerto 4200, puedes acceder usando esta ruta
```
http://localhost:4200/
```
### Uso
Esta será nuestro buscador. Al lado derecho tenemos el resumen de los ultimos 10 registros y el ratio de mutaciones
<img width="1912" height="924" alt="image" src="https://github.com/user-attachments/assets/d749960a-f059-4c6f-a09c-795babbc223d" />
En la parte izquierda tendremos el área de texto donde podemos interactuar escribiendo nuestra secuencia de ADN inicial.
<img width="1394" height="666" alt="image" src="https://github.com/user-attachments/assets/6e4e6bbc-dc20-42a8-ab68-b436bbe9b7b5" />
Haremos Click sobre `Find mutation` y obtendremos nuestro resultado
<img width="1912" height="924" alt="image" src="https://github.com/user-attachments/assets/e0d553c2-4cc4-49ff-ac75-15b8f221eab9" />
### Ejemplos de formatos de entrada
Sin mutación
```
A T G C G A
C A G T G C
T T A T T T
A G A C G G
G C G T C A
T C A C T G
```
Con mutación
```
A T G C G A
C A G T G C
T T A T G T
A G A A G G
C C C C T A
T C A C T G
```
### Ejemplos de consultas get a la api
Obtener Listado
```
http://18.191.36.88/api/list
```
Obtener Stats
```
http://18.191.36.88/api/stats
```
## Creditos
- Dulce Roxanna Clark Valenzuela ([@fungirox](https://github.com/fungirox))

[Análisis y proceso creativo](https://fungirox.notion.site/ERUS-DNA-Mutation-2939b177475d8023a850f8fba335f6c2?source=copy_link)
