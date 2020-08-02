import 'package:flutter/cupertino.dart';
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

  List<Object> images = List<Object>();
  Future<File> imageFile;
  String error = 'No Error Dectected';
  Future<bool> hasMediaAccess;

  @override
  void initState() {
    super.initState();
    setState(() {
      hasMediaAccess = requestAccess();
      images.add("add-button");
    });
  }

  Future<bool> requestAccess() async {
    return await Permission.camera.request().isGranted && await Permission.photos.request().isGranted;
  }

  @override
  Widget build(BuildContext context) {
    return Expanded(child: buildGridView(), flex: 0);
  }

  Widget buildGridView() {
    return GridView.count(
      shrinkWrap: true,
      physics: ScrollPhysics(),
      crossAxisCount: 3,
      childAspectRatio: 1,
      children: List.generate(images.length, (index) {
        if(images[index] == "add-button") {
          return Card(
            child: Column (
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.center,
              children: [
                IconButton(
                  icon: Icon(Icons.add),
                  onPressed: loadAssets
                ),
                Text('Add Images')
              ]
            )
          );
        } else {
          Asset asset = images[index];
          return Card(
            clipBehavior: Clip.antiAlias,
            child: Stack(
              children: <Widget>[
                AssetThumb(
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
                ),
                Positioned(
                  right: 5,
                  top: 5,
                  child: InkWell(
                    child: Icon(
                      Icons.remove_circle,
                      size: 20,
                      color: Colors.red,
                    ),
                    onTap: () {
                      setState(() {
                        images.removeAt(index);
                      });
                    },
                  ),
                ),
              ],
            ),
          );
        }
      }),
    );
  }

  Future<void> loadAssets() async {
    List<Asset> resultList = List<Asset>();
    List<Object> result = List<Object>();
    String error = 'No Error Dectected';

    try {
      resultList = await MultiImagePicker.pickImages(
        maxImages: 15,
        enableCamera: true,
        selectedAssets: images.where((element) => element is Asset).cast<Asset>().toList(),
        cupertinoOptions: CupertinoOptions(takePhotoIcon: "chat"),
        materialOptions: MaterialOptions(
          actionBarColor: "#41b8ea",
          actionBarTitle: "Select Images",
          allViewTitle: "All Photos",
          useDetailsView: false,
          statusBarColor: '#318bb0',
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

    result.addAll(resultList);
    result.add("add-button");

    setState(() {
      images = result;
      error = error;
    });
  }
}