
class Product {

  final int id;
  final String name;
  final String titleDescription;
  final int price;
  final String image;
  final String smallImage;
  final String thumbnail;
  final String latitude;
  final String longitude;
  final String description;
  final String condition;
  final String localGlobal;

  Product({this.id, this.name, this.titleDescription, this.price, this.image, this.smallImage,
      this.thumbnail, this.latitude, this.longitude, this.description, this.condition,
      this.localGlobal});

  factory Product.fromDoc(Map<dynamic, dynamic> doc) {
    return Product(
      id: doc['id'],
      name: doc['name_raw'][0],
      titleDescription: doc['title_description_raw'][0],
      price: doc['price_raw'][0],
      image: doc['image_raw'][0],
      smallImage: doc['small_image_raw'][0],
      thumbnail: doc['thumbnail_raw'][0],
      latitude: doc['latitude_raw'][0],
      longitude: doc['longitude_raw'][0],
      description: doc['description_raw'][0],
      condition: doc['condition_raw'][0],
      localGlobal: doc['local_global_raw'][0]
    );
  }
}