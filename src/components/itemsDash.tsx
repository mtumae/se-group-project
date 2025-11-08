import { useQuery } from '@tanstack/react-query'
import { convexQuery } from '@convex-dev/react-query'
import { api } from "../../convex/_generated/api";
import { CircleUser } from 'lucide-react';
import { Skeleton } from '@mui/material';
    



export default function ItemsDash() {
    const {data, isLoading, refetch} = useQuery(convexQuery(api.items.getAllItems, {}));
    return (
            <div className='mt-10'>
            <div className='flex justify-between items-center'>
                <h1>Item</h1>
                <h1>User</h1>
                <h1>Price</h1>
                <h1>Created At</h1>
            </div>
            <div>
                {isLoading && 
                <p>Loading...</p>
                }

                {data?
                    data.map((items) => (
                        <div key={items._id} className='border-b border-gray-200 py-4 flex justify-between items-center'>
                            <h2 className='text-lg flex-1 font-semibold'>{items.itemName}</h2>
                            <p className='flex-1'>{items.userId}</p>
                            <p className='text-sm flex-1 text-gray-500'>{items.price}ksh</p>
                            <p>{new Date(items.createdAt).toLocaleDateString()}</p>
                        </div>
                    ))
                : (
                    <p>No items found</p>
                )}
            </div>
        </div>
    )
}