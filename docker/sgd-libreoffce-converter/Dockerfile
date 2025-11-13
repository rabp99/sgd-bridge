# docker/sgd-libreoffice-converter/Dockerfile
# Usaremos una base Debian para una instalación de LibreOffice más sencilla.
FROM debian:bookworm-slim

# Instalar LibreOffice en modo headless (sin interfaz gráfica) y las dependencias de fuentes.
# Esto incrementará el tamaño, pero será una imagen confiable para la conversión.
RUN echo "deb http://deb.debian.org/debian bookworm main contrib non-free" > /etc/apt/sources.list \
    && echo "deb http://deb.debian.org/debian bookworm-updates main contrib non-free" >> /etc/apt/sources.list \
    && echo "deb http://deb.debian.org/debian-security bookworm-security main contrib non-free" >> /etc/apt/sources.list

# ⭐ PASO 2: INSTALAR PAQUETES ⭐
RUN apt-get update && apt-get install -y --no-install-recommends \
    libreoffice \
    libreoffice-writer \
    fonts-dejavu \
    unzip \
    ttf-mscorefonts-installer \
    cabextract \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/var/tmp/*

# El WORKDIR por defecto es /
WORKDIR /app

# Definir el comando de entrada si se desea un contenedor de ejecución continua
# ENTRYPOINT ["soffice"]
# CMD ["--accept=socket,host=127.0.0.1,port=2002;urp;"]
CMD ["tail", "-f", "/dev/null"]