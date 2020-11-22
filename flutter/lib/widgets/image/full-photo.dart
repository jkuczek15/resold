import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';
import 'package:photo_view/photo_view.dart';
import 'package:resold/constants/ui-constants.dart';

class FullPhoto extends StatelessWidget {
  final String title;
  final String url;

  FullPhoto(String title, {Key key, @required this.url})
      : title = title,
        super(key: key);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text(title, style: new TextStyle(color: Colors.white)),
        iconTheme: IconThemeData(
          color: Colors.white, //change your color here
        ),
        backgroundColor: ResoldBlue,
      ),
      body: FullPhotoScreen(url: url),
    );
  }
}

class FullPhotoScreen extends StatefulWidget {
  final String url;

  FullPhotoScreen({Key key, @required this.url}) : super(key: key);

  @override
  State createState() => FullPhotoScreenState(url: url);
}

class FullPhotoScreenState extends State<FullPhotoScreen> {
  final String url;

  FullPhotoScreenState({Key key, @required this.url});

  @override
  void initState() {
    super.initState();
  }

  @override
  Widget build(BuildContext context) {
    return Container(child: PhotoView(imageProvider: CachedNetworkImageProvider(url)));
  }
}
