## Build the project

### Build the zip

#### Build the free version

```
/bin/bash build/build_zip.sh base build/wc-wishlist-free.zip
```

#### Build the pro version

```
/bin/bash build/build_zip.sh pro build/wc-wishlist-pro.zip
```

### Install/Build

#### Build the free version ( no installation )

```
/bin/bash build/build.sh base false
```

#### Build the free version ( no installation )

```
/bin/bash build/build.sh pro false
```

## Swagger Codegen

### Usage

```
rm -rf srcFrontend/apiClientLib/src &&
java -jar bin/swagger-codegen-cli-3.0.34.jar generate -l typescript-axios -i swagger.json -o ./srcFrontend/apiClientLib/src
```

### Upgrading

[Download the last version as JAR from here](https://repo1.maven.org/maven2/io/swagger/codegen/v3/swagger-codegen-cli/)

---

## Usage of public api client

### Get all wishlists of the current user

```
await window.awlApi.wishlistApi.publicWishlistsGet()
```


## PHP Swagger

### [About](https://github.com/zircote/swagger-php)

### Compile swagger.json

```
./vendor/bin/openapi src/API -o swagger.json
```

