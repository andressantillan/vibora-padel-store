# Víbora Padel Store 🎾

# Proyecto - Aplicaciones Web

**Universidad Nacional de la Patagonia San Juan Bosco**

## Alumnos
- Andrés Santillán

## Cátedra
- Diego Martinez
- Gabriel Ingravallo
- Guillermo Urrutia

## Sitio web
[![Vibora Padel Store](https://img.shields.io/badge/🌐_Vibora_Padel_Store-000000?style=for-the-badge&logo=vercel&logoColor=white)](https://vibora-padel-store.vercel.app/)


## Descripción

Víbora Padel Store es una tienda online de artículos deportivos especializada en pádel. El proyecto es un e-commerce desarrollado con Laravel que permite a los usuarios explorar y adquirir productos relacionados con este deporte, como palas, pelotas, ropa, calzado y accesorios.

## Tecnologías utilizadas

- **Backend:** Laravel (PHP)
- **Frontend:** HTML, CSS, Vanilla JS, Bootstrap 5
- **Base de datos:** PostgreSQL
- **Deploy:** Vercel

## Características Principales

- Catálogo de productos deportivos para pádel.
- Carrito de compras y checkout.
- **Panel de administración completo** para gestionar productos, marcas, categorías, clientes y pedidos.

---

## 🛠️ Panel de Administración y Flujos de Trabajo

El sitio administrativo ha sido diseñado para gestionar de manera integral el ciclo de vida de las ventas. A continuación se detallan los flujos principales:

### 1. Gestión de Stock
La plataforma maneja un control estricto de inventario a nivel de **Variantes de Producto** (ej: Pala Head Delta Pro - Color: Negro, Peso: 370g).

*   **Reserva vs. Descuento Real:** El stock de un producto **no se descuenta cuando el cliente lo añade al carrito ni cuando crea el pedido**. Esto evita que carritos abandonados retengan stock infinitamente.
*   **Momento del Descuento:** El stock se descuenta de forma definitiva **únicamente cuando se registra el pago del pedido** (`onPaymentRegistered`).
*   **Stock Mínimo:** Cada variante tiene una alerta visual cuando su stock actual cae por debajo del umbral de "Stock mínimo" establecido.
*   **Seguridad:** No es posible eliminar físicamente una variante si posee stock superior a cero o si ya forma parte del historial de un pedido.

### 2. Flujo de Pedidos (Estados)
El estado general de un pedido avanza automáticamente en función de los eventos logísticos y financieros:

*   🟡 **Pendiente:** Estado inicial al crearse la orden. El cliente completó el checkout pero aún no hay pago registrado.
*   🔵 **Procesando:** El pago ha sido confirmado. En esta etapa, el personal del local debe preparar el paquete.
*   🟢 **Completado:** Se ha registrado un envío con éxito y el paquete fue despachado.
*   🔴 **Cancelado:** El pedido fue abortado. **Importante:** Solo se pueden cancelar manualmente aquellos pedidos que se encuentren en estado *Pendiente*.

### 3. Flujo de Pagos y Envíos
La gestión administrativa divide el cierre de una venta en dos grandes pasos:

*   **Registro de Pagos:** 
    *   Mientras el pedido está *Pendiente*, el administrador puede registrar un pago (Transferencia, MercadoPago, Efectivo).
    *   Al guardarlo, el estado de pago cambia a `Pagado`, se descuenta el stock de los productos y el pedido avanza a `Procesando`.
*   **Registro de Envíos:**
    *   La opción de registrar un envío **solo se habilita** una vez que el pedido figura como `Pagado`.
    *   El administrador selecciona el transporte (OCA, Andreani, Correo Argentino, etc.) e ingresa el código de seguimiento de la logística.
    *   Al guardar el envío, el estado del pedido avanza a su estado final de `Completado`.
    *   Si hay un error en la carga del envío, el administrador puede editar el transporte o código de seguimiento a posteriori.