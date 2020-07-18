
class Product {

  final int id;
  final String name;
  final String titleDescription;
  final int price;
  final String image;
  final String thumbnail;
  final String latitude;
  final String longitude;
  final String description;
  final String condition;
  final String localGlobal;

  Product({this.id, this.name, this.titleDescription, this.price, this.image,
      this.thumbnail, this.latitude, this.longitude, this.description, this.condition,
      this.localGlobal});

  factory Product.fromDoc(Map<dynamic, dynamic> doc) {
    return Product(
      id: doc['id'],
      name: doc['name_raw'][0]
    );
  }
}