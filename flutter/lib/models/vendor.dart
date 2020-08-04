class Vendor {

  final int id;
  final String name;
  final String about;
  final String profilePicture;

  Vendor({this.id, this.name, this.about, this.profilePicture});

  factory Vendor.fromJson(dynamic doc) {
    try {
      var vendor = Vendor(
        id: int.tryParse(doc['id']),
        name: doc['name'].toString().trim(),
        about: doc['about'].toString(),
        profilePicture: doc['profilePicture'].toString(),
      );

      return vendor;
    } catch (exception) {
      return Vendor();
    }
  }
}
