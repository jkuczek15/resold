class Product {

  int id;
  final String name;
  String titleDescription;
  final String price;
  final String image;
  final String smallImage;
  final String thumbnail;
  final String description;
  final String condition;
  final String localGlobal;
  double latitude;
  double longitude;
  List<int> categoryIds;
  int itemSize;

  Product({this.id, this.name, this.titleDescription, this.price, this.image, this.smallImage,
      this.thumbnail, this.latitude, this.longitude, this.description, this.condition,
      this.localGlobal, this.categoryIds, this.itemSize});

  factory Product.fromDoc(Map<dynamic, dynamic> doc) {
    try {
      var product = Product(
        id: doc['id'],
        name: doc['name_raw'][0].toString().trim(),
        price: doc['price_raw'][0].toString(),
        image: doc['image_raw'][0].toString(),
        smallImage: doc['small_image_raw'][0].toString(),
        thumbnail: doc['thumbnail_raw'][0].toString(),
        description: doc['description_raw'][0].toString().trim(),
        condition: doc['condition_raw'][0].toString(),
        localGlobal: doc['local_global_raw'][0].toString()
      );

      if(doc['title_description_raw'] != null && doc['title_description_raw'][0] != null) {
        product.titleDescription = doc['title_description_raw'][0].toString().trim();
      }

      if(doc['latitude_raw'] != null && doc['longitude_raw'] != null) {
        product.latitude = double.tryParse(doc['latitude_raw'][0].toString());
        product.longitude = double.tryParse(doc['longitude_raw'][0].toString());
      }

      if(doc['item_size'] != null) {
        product.itemSize = doc['item_size'];
      }

      return product;
    } catch (exception) {
      return Product();
    }
  }

  factory Product.fromJson(dynamic doc, {bool parseId = true}) {
    try {
      var product = Product(
          id: parseId ? int.tryParse(doc['id']) : doc['id'],
          name: doc['name'].toString().trim(),
          price: doc['price'].toString(),
          image: doc['image'].toString(),
          smallImage: doc['small_image'].toString(),
          thumbnail: doc['thumbnail'].toString(),
          description: doc['description'].toString().trim(),
          condition: doc['condition'].toString(),
          localGlobal: doc['local_global'].toString()
      );

      if(doc['title_description'] != null && doc['title_description'] != null) {
        product.titleDescription = doc['title_description'].toString().trim();
      }

      if(doc['latitude'] != null && doc['longitude'] != null) {
        product.latitude = double.tryParse(doc['latitude'].toString());
        product.longitude = double.tryParse(doc['longitude'].toString());
      }

      if(doc['item_size'] != null) {
        product.itemSize = int.tryParse(doc['item_size']);
      }

      return product;
    } catch (exception) {
      return Product();
    }
  }

  Map<String, dynamic> toJson() {
    try {
      return {
        'id': this.id,
        'name': this.name,
        'price': this.price,
        'image': this.image,
        'smallImage': this.smallImage,
        'thumbnail': this.thumbnail,
        'description': this.description,
        'titleDescription': this.titleDescription,
        'condition': this.condition,
        'localGlobal': this.localGlobal,
        'latitude': this.latitude,
        'longitude': this.longitude,
        'itemSize': this.itemSize
      };
    } catch (exception) {
      return {};
    }
  }
}