cmake_minimum_required(VERSION 3.6)
project(advanced)

set(CMAKE_CXX_FLAGS "${CMAKE_CXX_FLAGS} -std=c++11")

set(SOURCE_FILES
    Makefile
    uloha.c
    utils.c
    utils.h)

add_executable(advanced ${SOURCE_FILES})