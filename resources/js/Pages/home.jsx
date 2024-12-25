import React, { useState, useEffect } from 'react';
import { performSearch } from './Services/api';
import debounce from 'lodash.debounce';

export default function Home({ departmentsOptions }) {

    const [query, setQuery] = useState('');
    const [selectedDepartment, setSelectedDepartment] = useState('');
    const [searchResults, setSearchResults] = useState('');
    const [isLoading, seIsLoading] = useState(false);
    

    useEffect(() => {
        setSelectedDepartment(departmentsOptions[0].departmentId);
    }, []);


    // using debounce to avoid multiple calls while typing
    const handleSearchChange = debounce(async (event) => {
        const keyWord = event.target.value;
        setQuery(event.target.value);

        if (keyWord && selectedDepartment) {
            seIsLoading(true);

            // send search request
            const response = await doSearch(keyWord, selectedDepartment);
            setSearchResults(response);
            seIsLoading(false);
        } else {
            setSearchResults("");
        }
    }, 600); 


    const handleDepartmentChange = (e) => {
        const department = e.target.value;
        setSelectedDepartment(department);

        // if the search-bar is not empty perform seach
        // when changing the department as well
        if (query && department) {
            seIsLoading(true);
            doSearch(query, department)
                .then((response) => {
                    setSearchResults(response);
                    seIsLoading(false);
                });
        }
    };

    const doSearch = async (keyword, department) => {
        try {
            const params = {
                "query": keyword,
                "department_id": department
            };
            const response = await performSearch(params);  
            return response.data;
        } catch (error) {
            console.error("Search failed:", error); // Log the error for debugging
            return [];
        }

    }


    return (
        <>
            <div>
                {/* Navbar section */}
                <nav className="p-4 flex items-start">
                    <div className="text-white text-2xl ml-20">
                    <img src="/images/logo.png" alt="Logo" className="h-14" />
                    </div>
                </nav>
                {/* Navbar section ends */}


                {/* Welcome section */}
                <div className="flex justify-center mt-12">
                    <div className="w-1/2">
                        <h3 className='text-lg text-gray-600'>Hello!</h3>
                        <p className="text-sm text-gray-600 mb-6">
                            We offer information on over <span className="font-bold">470,000 artworks</span> across <span className="font-bold">19 departments</span>, including "Drawings and Prints" "Egyptian Art" "European Paintings" and more. 
                        </p>
                        <p className="text-sm text-gray-600">
                            Please select a department below and search for the artwork title you're looking for. We're here to help you discover amazing art!
                        </p>
                    </div>
                </div>
                {/* Welcome section ends */}

                {/* Form section */}
                <div className="flex justify-center mt-24">
                    <div className="w-1/2 flex flex-col space-y-2">
                        <label htmlFor="departments" className="text-gray-700 font-medium">Department</label>
                        <select
                            id="departments"
                            name="departments"
                            className="focus:ring-[#0d98ba] focus:outline-none w-full p-2 rounded-md text-gray-700 border border-gray-300 focus:ring-2 appearance-none bg-transparent"
                            required
                            onChange={(e) => {
                                handleDepartmentChange(e);
                            }}
                            >
                            {departmentsOptions.map((department) => (
                                <option key={department.departmentId} value={department.departmentId}>
                                    {department.displayName}
                                </option>
                            ))}
                        </select>
                    </div>
                </div>
                <div className="flex justify-center mt-4">
                    <div className="w-1/2 flex items-center space-x-2">
                    <input
                        id="search"
                        type="text"
                        placeholder="Search..."
                        className="focus:ring-[#0d98ba] focus:outline-none w-full p-2 rounded-md text-gray-700 border border-gray-300 focus:ring-2"
                        onChange={(e) => {
                            handleSearchChange(e);
                        }}
                    />
                    </div>
                </div>
                {/* Form section ends */}

                {/* Results section */}
                <div className="max-w-2xl mx-auto mt-8 mb-32">
                    {isLoading ? (
                        <div className="flex justify-center items-center space-x-2">
                            <div className="w-12 h-12 border-4 border-t-transparent border-solid rounded-full animate-spin-slow border-[#0d98ba]"></div>
                            <span className="text-gray-500 animate-pulse">Loading...</span>
                        </div>
                    ) : searchResults === "" ? (
                        null
                    ) : searchResults.length > 0 ? (
                        <ul className="space-y-4">
                            {searchResults.map((result, index) => (
                                <li key={index}>
                                    <a
                                        target="_blank"
                                        href={result.object_url}
                                        className="focus:ring-[#0d98ba] focus:outline-none block p-4 border border-gray-300 rounded-lg hover:bg-gray-100 focus:ring-2"
                                    >
                                        <h3 className="text-md font-semibold text-gray-900 hover:text-[#0d98ba]">{result.title}</h3>
                                    </a>
                                </li>
                            ))}
                        </ul>
                    ) : (
                        <p className="text-center text-gray-500">No results found.</p>
                    )}
                </div>
                {/* Results section ends */}
            </div>
        </>
    )
}