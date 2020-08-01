class Product {

  final int id;
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

  Product({this.id, this.name, this.titleDescription, this.price, this.image, this.smallImage,
      this.thumbnail, this.latitude, this.longitude, this.description, this.condition,
      this.localGlobal});

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

      return product;
    } catch (exception) {
      return Product();
    }
  }
}