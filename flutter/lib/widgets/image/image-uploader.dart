import 'dart:async';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:multi_image_picker/multi_image_picker.dart';
import 'package:permission_handler/permission_handler.dart';
import 'package:resold/services/resold.dart';
import 'package:resold/state/actions/set-sell-image-state.dart';
import 'package:resold/state/screens/sell/sell-image-state.dart';
import 'package:resold/widgets/loading.dart';

class ImageUploader extends StatelessWidget {
  final List<Asset> images;
  final List<String> imagePaths;
  final Function dispatcher;

  ImageUploader({this.images, this.imagePaths, this.dispatcher});

  Future<bool> requestAccess() async {
    return await Permission.camera.request().isGranted && await Permission.photos.request().isGranted;
  }

  @override
  Widget build(BuildContext context) {
    requestAccess();
    return Expanded(child: buildGridView(context), flex: 0);
  }

  Widget buildGridView(context) {
    return GridView.count(
      shrinkWrap: true,
      physics: ScrollPhysics(),
      crossAxisCount: 3,
      childAspectRatio: 1,
      children: List.generate(images.length + 1, (index) {
        if (index == images.length) {
          return Card(
              child: Column(
                  mainAxisAlignment: MainAxisAlignment.center,
                  crossAxisAlignment: CrossAxisAlignment.center,
                  children: [IconButton(icon: Icon(Icons.add), onPressed: () => loadAssets(context)), Text('Add')]));
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
                      child: Loading(),
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
                    onTap: () async {
                      // show a loading indicator
                      showDialog(
                          context: context,
                          builder: (BuildContext context) {
                            return Center(child: Loading());
                          });
                      await Resold.deleteImage(imagePaths[index]);
                      Navigator.of(context, rootNavigator: true).pop('dialog');
                      images.removeAt(index);
                      imagePaths.removeAt(index);
                      dispatcher(SetSellImageStateAction(SellImageState(images: images, imagePaths: imagePaths)));
                    },
                  ),
                ),
              ],
            ),
          );
        }
      }),
    );
  } // end function buildGridView

  Future<void> loadAssets(BuildContext context) async {
    List<Asset> resultList = List<Asset>();

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
        ));

    // show a loading indicator
    showDialog(
        context: context,
        builder: (BuildContext context) {
          return Center(child: Loading());
        });

    // upload the images to the server
    var paths = await Resold.uploadImages(resultList);

    Navigator.of(context, rootNavigator: true).pop('dialog');

    dispatcher(SetSellImageStateAction(SellImageState(images: resultList, imagePaths: paths)));
  } // end function loadAssets
}
