# Aumentar y Ajustar la Sensibilidad del Escáner de Códigos de Barras

La aplicación utiliza la librería `html5-qrcode` para la lectura de códigos de barras tanto en el módulo de ventas (Dashboard del cajero) como en la creación de productos (Admin). Si el escáner tiene problemas de sensibilidad (le cuesta enfocar o es muy lento reconociendo códigos), puedes ajustar su configuración.

## Parámetros clave de sensibilidad y rendimiento

Para modificar la sensibilidad, debes editar el objeto de configuración pasado al constructor de `Html5QrcodeScanner` en los archivos correspondientes.

Los archivos donde se encuentra la configuración son:
- `resources/views/cajero/dashboard.blade.php`
- `resources/views/admin/productos/create.blade.php`

### Ejemplo de Configuración Optimizada

```javascript
let html5QrcodeScanner = new Html5QrcodeScanner(
    "reader",
    {
        fps: 30, // Mayor cantidad de cuadros por segundo (default era 10)
        qrbox: {width: 250, height: 150},
        useBarCodeDetectorIfSupported: true, // Usa API nativa del navegador (mucho más rápido)
        showTorchButtonIfSupported: true,    // Muestra botón de flash para mejorar la iluminación
        rememberLastUsedCamera: true         // Recuerda la última cámara usada
    },
    /* verbose= */ false
);
```

### Explicación de los parámetros

1. **`fps` (Frames Per Second):**
   Determina la frecuencia con la que la librería intenta leer el video.
   - **Valor bajo (ej. 10):** Ahorra batería pero la lectura es lenta y parece poco sensible.
   - **Valor alto (ej. 30 o 60):** Consume más recursos pero mejora notablemente la velocidad y "sensibilidad" con la que atrapa el código cuando pasa por la cámara. **Recomendado: 30**.

2. **`useBarCodeDetectorIfSupported`:**
   Al establecerlo en `true`, la librería intentará usar la **BarcodeDetector API** nativa del navegador (disponible en Chrome y Edge modernos, especialmente en Android).
   - La API nativa usa aceleración por hardware y es increíblemente más rápida y sensible que el procesamiento en JavaScript puro.

3. **`qrbox` (Área de escaneo):**
   Define el tamaño del recuadro donde el usuario debe colocar el código.
   - Si el recuadro es demasiado pequeño o tiene proporciones erróneas, será difícil enfocar.
   - Para códigos de barras 1D (como los de productos), un recuadro rectangular (ej: `width: 250, height: 150`) suele funcionar mejor que uno cuadrado perfecto.

4. **`showTorchButtonIfSupported`:**
   Permite encender el flash del celular. Una mala iluminación es la causa número uno de la "baja sensibilidad" en las cámaras de los celulares. Encender el flash mejora drásticamente el contraste del código de barras.

5. **`videoConstraints` (Avanzado):**
   Si necesitas forzar una resolución mayor para que la cámara enfoque códigos pequeños, puedes agregar este parámetro (esto puede hacer la aplicación más pesada):
   ```javascript
   videoConstraints: {
       facingMode: "environment",
       width: { min: 1280, ideal: 1920 },
       height: { min: 720, ideal: 1080 }
   }
   ```
   *Nota: Solo añade `videoConstraints` si los pasos anteriores no son suficientes y necesitas forzar cámaras de alta resolución, ya que algunos dispositivos antiguos pueden no soportarlo.*

## ¿Cómo aplicarlo?

1. Abre el archivo donde deseas mejorar la lectura, por ejemplo `resources/views/cajero/dashboard.blade.php`.
2. Busca la línea donde se instancia `new Html5QrcodeScanner`.
3. Reemplaza o añade las propiedades mencionadas en el objeto de configuración.
4. Guarda y actualiza la página en el navegador (asegúrate de limpiar la caché del navegador si los cambios no se reflejan).
