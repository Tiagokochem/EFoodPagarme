import 'package:efood_multivendor/controller/auth_controller.dart';
import 'package:efood_multivendor/controller/notification_controller.dart';
import 'package:efood_multivendor/controller/splash_controller.dart';
import 'package:efood_multivendor/helper/date_converter.dart';
import 'package:efood_multivendor/helper/route_helper.dart';
import 'package:efood_multivendor/util/dimensions.dart';
import 'package:efood_multivendor/util/images.dart';
import 'package:efood_multivendor/util/styles.dart';
import 'package:efood_multivendor/view/base/custom_app_bar.dart';
import 'package:efood_multivendor/view/base/custom_image.dart';
import 'package:efood_multivendor/view/base/no_data_screen.dart';
import 'package:efood_multivendor/view/base/not_logged_in_screen.dart';
import 'package:efood_multivendor/view/screens/notification/widget/notification_dialog.dart';
import 'package:flutter/material.dart';
import 'package:get/get.dart';

class NotificationScreen extends StatefulWidget {
  final bool fromNotification;
  const NotificationScreen({Key? key, this.fromNotification = false}) : super(key: key);

  @override
  State<NotificationScreen> createState() => _NotificationScreenState();
}

class _NotificationScreenState extends State<NotificationScreen> {
  void _loadData() async {
    Get.find<NotificationController>().clearNotification();
    if(Get.find<SplashController>().configModel == null) {
      await Get.find<SplashController>().getConfigData();
    }
    if(Get.find<AuthController>().isLoggedIn()) {
      Get.find<NotificationController>().getNotificationList(true);
    }
  }
  @override
  void initState() {
    super.initState();
    _loadData();
  }

  @override
  Widget build(BuildContext context) {


    return WillPopScope(
      onWillPop: () async {
        if(widget.fromNotification) {
          Get.offAllNamed(RouteHelper.getInitialRoute());
          return true;
        }else {
          Get.back();
          return true;
        }
      },
      child: Scaffold(
        appBar: CustomAppBar(title: 'notification'.tr, onBackPressed: () {
          if(widget.fromNotification) {
            Get.offAllNamed(RouteHelper.getInitialRoute());
          }else {
            Get.back();
          }
        }),
        body: Get.find<AuthController>().isLoggedIn() ? GetBuilder<NotificationController>(builder: (notificationController) {
          if(notificationController.notificationList != null) {
            notificationController.saveSeenNotificationCount(notificationController.notificationList!.length);
          }
          List<DateTime> dateTimeList = [];
          return notificationController.notificationList != null ? notificationController.notificationList!.isNotEmpty ? RefreshIndicator(
            onRefresh: () async {
              await notificationController.getNotificationList(true);
            },
            child: Scrollbar(child: SingleChildScrollView(
              physics: const AlwaysScrollableScrollPhysics(),
              child: Center(child: SizedBox(width: Dimensions.webMaxWidth, child: ListView.builder(
                itemCount: notificationController.notificationList!.length,
                padding: const EdgeInsets.all(Dimensions.paddingSizeSmall),
                physics: const NeverScrollableScrollPhysics(),
                shrinkWrap: true,
                itemBuilder: (context, index) {
                  DateTime originalDateTime = DateConverter.dateTimeStringToDate(notificationController.notificationList![index].createdAt!);
                  DateTime convertedDate = DateTime(originalDateTime.year, originalDateTime.month, originalDateTime.day);
                  bool addTitle = false;
                  if(!dateTimeList.contains(convertedDate)) {
                    addTitle = true;
                    dateTimeList.add(convertedDate);
                  }
                  return Column(crossAxisAlignment: CrossAxisAlignment.start, children: [

                    addTitle ? Padding(
                      padding: const EdgeInsets.only(bottom: Dimensions.paddingSizeExtraSmall),
                      child: Text(DateConverter.dateTimeStringToDateOnly(notificationController.notificationList![index].createdAt!)),
                    ) : const SizedBox(),

                    InkWell(
                      onTap: () {
                        showDialog(context: context, builder: (BuildContext context) {
                          return NotificationDialog(notificationModel: notificationController.notificationList![index]);
                        });
                      },
                      child: Padding(
                        padding: const EdgeInsets.symmetric(vertical: Dimensions.paddingSizeExtraSmall),
                        child: Row(children: [

                          ClipOval(child: CustomImage(
                            height: 40, width: 40, fit: BoxFit.cover, placeholder: Images.notificationPlaceholder,
                            image: '${Get.find<SplashController>().configModel!.baseUrls!.notificationImageUrl}'
                                '/${notificationController.notificationList![index].data!.image}',
                          )),
                          const SizedBox(width: Dimensions.paddingSizeSmall),

                          Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
                            Text(
                              notificationController.notificationList![index].data!.title ?? '', maxLines: 1, overflow: TextOverflow.ellipsis,
                              style: robotoMedium.copyWith(fontSize: Dimensions.fontSizeSmall),
                            ),
                            Text(
                              notificationController.notificationList![index].data!.description ?? '', maxLines: 1, overflow: TextOverflow.ellipsis,
                              style: robotoRegular.copyWith(fontSize: Dimensions.fontSizeSmall),
                            ),
                          ])),

                        ]),
                      ),
                    ),

                    Padding(
                      padding: const EdgeInsets.only(left: 50),
                      child: Divider(color: Theme.of(context).disabledColor, thickness: 1),
                    ),

                  ]);
                },
              ))),
            )),
          ) : NoDataScreen(text: 'no_notification_found'.tr) : const Center(child: CircularProgressIndicator());
        }) : const NotLoggedInScreen(),
      ),
    );
  }
}
