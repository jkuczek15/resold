import 'package:flutter/material.dart';
import 'dart:io';
import 'dart:async';
import 'package:multi_image_picker/multi_image_picker.dart';
import 'package:permission_handler/permission_handler.dart';

class ImageUploader extends StatefulWidget {
  @override
  ImageUploaderState createState() {
    return ImageUploaderState();
  }
}

class ImageUploaderState extends State<ImageUploader> {

  List<Asset> images = List<Asset>();
  Future<File> imageFile;
  String error = 'No Error Dectected';
  Future<bool> hasFileAccess;

  @override
  void initState() {
    super.initState();
    setState(() {
      hasFileAccess = requestAccess();
    });
  }

  Future<bool> requestAccess() async {
    return await Permission.camera.request().isGranted && await Permission.photos.request().isGranted;
  }

  @override
  Widget build(BuildContext context) {
    return Column(
      children: [
        SizedBox(height: 10),
        RaisedButton(
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadiusDirectional.circular(8)
          ),
          onPressed: loadAssets,
          child: Text('Select Images',
            style: new TextStyle(
                fontSize: 20.0,
                fontWeight: FontWeight.bold,
                color: Colors.white
            )
          ),
          padding: EdgeInsets.fromLTRB(105, 30, 105, 30),
          color: Colors.black,
          textColor: Colors.white,
        ),
        SizedBox(height: 10),
        Expanded(
          child: buildGridView(),
        )
      ],
    );
  }

  Widget buildGridView() {
    return GridView.count(
      crossAxisCount: 3,
      children: List.generate(images.length, (index) {
        Asset asset = images[index];
        return Padding (
          padding: EdgeInsets.fromLTRB(2, 2, 2, 2),
          child: AssetThumb(
           asset: asset,
           width: 300,
           height: 300,
           spinner: Center(
             child: SizedBox(
               width: 50,
               height: 50,
               child: CircularProgressIndicator(backgroundColor: const Color(0xff41b8ea)),
             ),
           ),
          )
        );
      })
    );
  }

  Future<void> loadAssets() async {
    List<Asset> resultList = List<Asset>();
    String error = 'No Error Dectected';

    try {
      resultList = await MultiImagePicker.pickImages(
        maxImages: 300,
        enableCamera: true,
        selectedAssets: images,
        cupertinoOptions: CupertinoOptions(takePhotoIcon: "chat"),
        materialOptions: MaterialOptions(
          actionBarColor: "#41b8ea",
          actionBarTitle: "Select Images",
          allViewTitle: "All Photos",
          useDetailsView: false,
          statusBarColor: '#41b8ea',
          selectCircleStrokeColor: "#41b8ea",
        )
      );
    } on Exception catch (e) {
      error = e.toString();
    }

    // If the widget was removed from the tree while the asynchronous platform
    // message was in flight, we want to discard the reply rather than calling
    // setState to update our non-existent appearance.
    if (!mounted) return;

    setState(() {
      images = resultList;
      error = error;
    });
  }
}